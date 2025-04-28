<?php

namespace ProcessWire;

class FieldtypeTwitch extends FieldtypeText
{
    public static function getModuleInfo()
    {
        return [
            'title'    => 'Twitch',
            'version'  => 2,
            'summary'  => 'Stores a Twitch username and retrieves live data',
            'author'   => 'TwoWheelDev',
            'requires' => ['InputfieldTwitch'],
        ];
    }

    public function getInputfield(Page $page, Field $field)
    {
        /** @var InputfieldTwitch $inputfield */
        $inputfield = $this->modules->get('InputfieldTwitch');
        $inputfield->description('Enter a Twitch username');

        return $inputfield;
    }
}
