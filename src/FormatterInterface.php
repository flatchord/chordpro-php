<?php

namespace ChordPro;

interface FormatterInterface {
    public const SHARP_SYMBOL = '&#9839;'; // ♯
    public const NATURAL_SYMBOL = '&#9838;'; // ♮
    public const FLAT_SYMBOL = '&#9837;'; //

    public function format(Song $song, array $options): string;
}
