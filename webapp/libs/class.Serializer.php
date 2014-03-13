<?php
class Serializer {
    /**
     * Unserialize a string.
     * @param str $string
     * @return mixed Unserialized data
     * @throws SerializerException
     */
    public static function unserializeString($serialized_string) {
        if (empty($serialized_string)) {
            throw new SerializerException('Cannot unserialize an empty string');
        }
        $result = unserialize($serialized_string);
        if ($result === false) {
            throw new SerializerException('String is unserializable');
        }
       return $result;
    }
}