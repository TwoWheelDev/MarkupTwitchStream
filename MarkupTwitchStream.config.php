<?php

namespace ProcessWire;

class markupTwitchStreamConfig extends ModuleConfig
{
    public function getInputfields() {
        $fields = parent::getInputfields();

        $field = $this->modules->get('InputfieldText');
        $field->name = 'clientId';
        $field->label = 'Twitch Client ID';
        $field->value = $data['clientId'] ?? '';
        $fields->add($field);

        /** @var InputfieldText $field */
        $field = $this->modules->get('InputfieldText');
        $field->name = 'clientSecret';
        $field->label = 'Twitch Client Secret';
        $field->value = $data['clientSecret'] ?? '';
        $field->type = 'password';
        $fields->add($field);

        return $fields;
    }
}
