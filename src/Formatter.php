<?php

namespace ChordPro;

class Formatter {
    protected bool $hasFrenchChords = false;
    protected bool $hasNoChords = false;

    public function setOptions(Song $song, array $options)
    {
        if (isset($options['french']) && $options['french'] === true) {
            $this->hasFrenchChords = true;
        }

        if (isset($options['no_chords']) && $options['no_chords'] === true) {
            $this->hasNoChords = true;
        }
    }
}
