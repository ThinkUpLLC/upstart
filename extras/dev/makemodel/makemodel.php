<?php
/**
 *
 * ThinkUp/extras/dev/makemodel/makemodel.php
 *
 * Copyright (c) 2011-2013 Gina Trapani
 *
 * LICENSE:
 *
 * This file is part of ThinkUp (http://thinkup.com).
 *
 * ThinkUp is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 2 of the License, or (at your option) any
 * later version.
 *
 * ThinkUp is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with ThinkUp.  If not, see
 * <http://www.gnu.org/licenses/>.
 *
 * @author Gina Trapani <ginatrapani[at]gmail[dot]com>
 * @license http://www.gnu.org/licenses/gpl.html
 * @copyright 2011-2013 Gina Trapani
 *
 * Usage:
 * makemode.php <table_name> <object_name> [<parent_object_name>]
 */
chdir('..');
chdir('..');
chdir('..');
chdir('webapp');
require_once 'init.php';
chdir('..');

Loader::register(array(
dirname(__FILE__) . '/classes/'
));

if (isset($argv) && sizeof($argv) > 2) {
    $object = new ModelMaker($argv[1], $argv[2]);
    echo $object->makeModel();
} else {
    echo "Usage:
    makemodel.php <table_name> <object_name> [<parent_object_name>]
";
}
