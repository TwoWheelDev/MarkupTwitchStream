<?php

namespace ProcessWire;

class InputfieldTwitch extends InputfieldText
{
    public static function getModuleInfo()
    {
        return [
            'title'    => 'Twitch',
            'version'  => 2,
            'summary'  => 'Enter a Twitch username and view stream info',
            'author'   => 'TwoWheelDev',
            'requires' => 'MarkupTwitchStream',
        ];
    }

    public function render()
    {
        $out = parent::render();

        $username = $this->value;
        if (!$username) {
            return $out;
        }

        /** @var TwitchAPI $api */
        $api = $this->wire('modules')->get('MarkupTwitchStream');
        $streamData = $api->getStreamStatus($username);

        if ($streamData) {
            $out .= "<div class='twitch-status'><strong>LIVE</strong>: ".$streamData['title'].'</div>';
        } else {
            $out .= "<div class='twitch-status'>Offline or not found</div>";
        }

        return $out;
    }
}
