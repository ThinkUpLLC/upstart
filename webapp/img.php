<?php
require_once 'init.php';
/**
 * Image proxy and cache, and comparator
 *
 * Proxy-cache
 * -----------
 * Accepts 3 $_GET parameters:
 * t (type) = either 'avatar' or 'favicon'
 * url = valid URL
 * s (signature) = a token proving the loader is legit
 *
 * v 1.0
 * If request has a valid signature and is for an avatar:
 * If request is for a Twitter avatar (ie, from pbs.twimg.com)
 *     Retrieve image from filesystem
 *     If it exists, return it
 *     If it does not exist, request URL
 *     If URL is 200, save to file system and return file
 *     If URL is 404, return blank image
 * else redirect to the url
 * else redirect to the url
 *
 * Filesystem store: md5($url)
 *
 * v 2.0
 * Add support for favicons
 *
 * Comparator
 * ----------
 * Accepts 3 $_GET parameters
 * * image1
 * * image2
 * * s (signature) = a token proving the loader is legit
 *
 * v 1.0
 * If request has a valid signature and URLs for two images:
 * If both images exist on file system and reasonably match
 *  return JSON result show_diff and reason
 */

class ImageProxyCacheController extends Controller {
    /**
     * URL of image getting cached and displayed
     * @var str
     */
    var $url;
    /**
     * Local filename of the image being cached and displayed.
     * @var str
     */
    var $local_filename;

    public function control() {
        if ($this->isValidImageRequest()) {
            $this->url = $_GET['url'];
            $parsed_url = parse_url($this->url);

            //Only cache Twitter avatars
            if ($parsed_url['host'] == 'pbs.twimg.com') {
                //Get the path
                $url_path = $parsed_url['path'];
                //Get the file extension
                $extension = pathinfo($url_path, PATHINFO_EXTENSION);
                //Construct the local cache filename
                $this->local_filename = $this->getLocalFilename($this->url, $extension);

                if (file_exists($this->local_filename)) {
                    $type = 'image/'.$extension;
                    header('Content-Type:'.$type);
                    header('Content-Length: ' . filesize($this->local_filename));
                    readfile($this->local_filename);
                } else {
                    $blank_image = (FileDataManager::getDataPath()).'image-cache/avatars/blank.png';
                    $this->cacheAndDisplayImage($this->url, $this->local_filename, $blank_image, $extension);
                }
            } else {
                $this->redirect($this->url);
            }
        } elseif ($this->isValidComparisonRequest()) {
            $url_a = $_GET['image1'];
            $parsed_url_a = parse_url($url_a);
            $url_path_a = $parsed_url_a['path'];
            $url_extension_a = pathinfo($url_path_a, PATHINFO_EXTENSION);
            $file_a = $this->getLocalFilename($url_a, $url_extension_a);

            $url_b = $_GET['image2'];
            $parsed_url_b = parse_url($url_b);
            $url_path_b = $parsed_url_b['path'];
            $url_extension_b = pathinfo($url_path_b, PATHINFO_EXTENSION);
            $file_b = $this->getLocalFilename($url_b, $url_extension_b);

            $reason = '';
            $show_diff = false;
            if (file_exists($file_a) && file_exists($file_b)) {
                if ($this->doFilesMatch($file_a, $file_b)) {
                    $show_diff = false;
                    $reason = 'Files match';
                } else {
                    $show_diff = true;
                    $reason = 'Files do no match';
                }
            } else {
                $show_diff = false;
                $reason = 'One or both files do not exist';
            }
            $this->setJsonData(array('show_diff'=>$show_diff, 'reason'=>$reason));
            return $this->generateView();
        } else {
            $this->redirect($this->url);
        }
    }
    /**
     * Check if the request for an image is valid - has all required parameters and a valid signature.
     * @return bool
     */
    private function isValidImageRequest() {
        return (
            $this->isSignatureValid()
            && isset($_GET['url'])
            //image type
            && (isset($_GET['t']) && $_GET['t'] == 'avatar')
        );
    }
    /**
     * Check if the request for an image is valid - has all required parameters and a valid signature.
     * @return bool
     */
    private function isValidComparisonRequest() {
        return (
            $this->isSignatureValid()
            && isset($_GET['image1'])
            && isset($_GET['image2'])
        );
    }
    /**
     * Check if the signature is valid.
     * @return bool
     */
    private function isSignatureValid() {
        $cfg = Config::getInstance();
        $passphrase = $cfg->getValue('image_proxy_passphrase');

        return (
            //request signature is a simple MD5 hash of a passphrase
            isset($_GET['s']) && $_GET['s'] == md5($passphrase)
        );
    }
    /**
     * Determine the full path and filename for a given URL.
     * @param  str $url
     * @param  str $extension File extension of image
     * @return str Full local file path
     */
    private function getLocalFilename($url, $extension) {
        $data_path = FileDataManager::getDataPath();
        $local_file = $data_path.'image-cache/avatars/'.(md5($url).'.'.$extension);
        return $local_file;
    }
    /**
     * Cache and display image. If image exists on filesystem, retrieve. If not, call the URL and save it to filesystem
     * or return blank image if not a 200.
     * @param  str $url
     * @param  str $local_filename
     * @param  str $blank
     * @param  str $extension
     * @return void
     */
    private function cacheAndDisplayImage($url, $local_filename, $blank, $extension){
        $ch = curl_init ($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        $useragent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1";
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $raw = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // print_r($url);
        // print_r($status);
        // print_r($raw);
        curl_close ($ch);
        if ($status == 200) {
            if (file_exists($local_filename)){
                unlink($local_filename);
            }
            $fp = fopen($local_filename,'x');
            $file = $local_filename;
            $type = 'image/'.$extension;
            if (fwrite($fp, $raw) === false) {
                //@TODO throw an Exception
                $file = $blank;
                $type = 'image/png';
            }
            fclose($fp);
        } else {
            $file = $blank;
            $type = 'image/png';
        }
        header('Content-Type:'.$type);
        header('Content-Length: ' . filesize($file));
        readfile($file);
    }
    /**
     * Tell whether or not 2 files are the same.
     * This is a rudimentary, hit-or-miss file comparison.
     * @param  str $file_a
     * @param  str $file_b
     * @return bool
     */
    private function doFilesMatch($file_a, $file_b) {
        if (filesize($file_a) == filesize($file_b)) {
            $fp_a = fopen($file_a, 'rb');
            $fp_b = fopen($file_b, 'rb');

            while (($b = fread($fp_a, 4096)) !== false) {
                $b_b = fread($fp_b, 4096);
                if ($b !== $b_b) {
                    fclose($fp_a);
                    fclose($fp_b);
                    return false;
                }
            }
            fclose($fp_a);
            fclose($fp_b);
            return true;
        }
        return false;
    }
}

$controller = new ImageProxyCacheController();
echo $controller->control();

