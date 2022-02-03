<?php

namespace ChordPro;

class Block {

    private string|null $chord;

    private string|null $text;

    private array $frenchChords = [
        'A' => 'La',
        'B' => 'Si',
        'C' => 'Do',
        'D' => 'Ré',
        'E' => 'Mi',
        'F' => 'Fa',
        'G' => 'Sol',
    ];

    public function __construct($chord, $text)
    {
        $this->chord = $chord;
        $this->text = $text;
    }

    private function englishNotation(string $chord): string
    {
        if (!in_array(substr(strtolower($chord),0,2),['la','si','do','ré','re','mi','fa','so'])) {
            return $chord;
        }

        $frArr = [];
        $enArr = [];

        foreach ($this->frenchChords as $k => $v) {
            $frArr[] = strtolower($v);
            $enArr[] = $k;
        }

        return str_replace($frArr, $enArr, strtolower($chord));
    }

    public function getFrenchChord(): array
    {
        $chords = explode('/',$this->chord);
        $result = [];

        foreach ($chords as $chord) {
            if (strlen($chord) > 0 && isset($this->frenchChords[$chord])) {
                $result[] = [
                    $this->frenchChords[substr($chord, 0, 1)],
                    substr($chord, 1),
                ];
            }
        }

        return $result;
    }

    public function getChord(): array
    {
        $result = [];

        if (!is_null($this->chord)) {
            $chords = explode('/', $this->englishNotation($this->chord));

            foreach ($chords as $chord) {
                $result[] = [
                    substr($chord, 0, 1),
                    substr($chord, 1),
                ];
            }
        }

        return $result;
    }

    public function getText(): string|null
    {
        return $this->text;
    }

    public function setChord(string $chord)
    {
        $this->chord = $chord;
    }
}
