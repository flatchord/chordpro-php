<?php

namespace ChordPro;

class HtmlFormatter extends Formatter implements FormatterInterface
{
    public function format(Song $song, array $options): string
    {
        $this->setOptions($song,$options);
        $html = '';

        foreach ($song->lines as $line) {
            if (is_null($line)) {
                $html .= '<br />';

                continue;
            }

            $html .= $this->getLineHtml($line);
        }

        return $html;
    }

    private function getLineHtml(Line $line): string
    {
        if ($line instanceof Metadata) {
            return $this->getMetadataHtml($line);
        }

        if ($line instanceof Lyrics) {
            return $this->hasNoChords ? $this->getLyricsOnlyHtml($line) : $this->getLyricsHtml($line);
        }

        return '';
    }

    private function blankChars(string|null $text): string
    {
        if (is_null($text)) {
            $text= '&nbsp;';
        }

        return str_replace(' ', '&nbsp;', $text);
    }

    private function getMetadataHtml(Metadata $metadata): string
    {
        return match ($metadata->getName()) {
            'start_of_chorus' =>
                (
                    !is_null($metadata->getValue())
                        ? '<div class="chordpro-chorus-comment">' . $metadata->getValue() . '</div>'
                        : ''
                )
                . '<div class="chordpro-chorus">',
            'end_of_chorus' => '</div>',
            default => '<div class="chordpro-' . $metadata->getName() . '">' . $metadata->getValue() . '</div>',
        };
    }

    private function getLyricsHtml(Lyrics $lyrics): string
    {
        $verse = '<div class="chordpro-verse">';

        foreach ($lyrics->getBlocks() as $block) {
            $chords = [];
            $sliced_chords = (true === $this->hasFrenchChords) ? $block->getFrenchChord() : $block->getChord();

            if (is_array($sliced_chords)) {
                foreach ($sliced_chords as $sliced_chord) {
                    // Test if minor/major presence before slice chord with exposant part
                    if (strtolower(substr($sliced_chord[1], 0, 1)) == 'm') { // in first position (without alteration)
                        $chords[] = $sliced_chord[0] . substr($sliced_chord[1], 0, 1) . '<sup>' . substr($sliced_chord[1], 1) . '</sup>';
                    } elseif (strtolower(substr($sliced_chord[1], 1, 1)) == 'm') { // in second position (with alteration)
                        $chords[] = $sliced_chord[0] . '<sup>' . substr($sliced_chord[1], 0, 1) . '</sup>' . substr($sliced_chord[1], 1, 1) . '<sup>' . substr($sliced_chord[1], 2) . '</sup>';
                    } else {
                        $chords[] = $sliced_chord[0] . '<sup>' . substr($sliced_chord[1], 0) . '</sup>';
                    }
                }
            }

            $chord = implode('/', $chords);
            $chord = $this->blankChars(str_replace(['#', 'b', 'K'], [static::SHARP_SYMBOL, static::FLAT_SYMBOL, static::NATURAL_SYMBOL], $chord));
            $text = $this->blankChars($block->getText());
            $verse .= '<span class="chordpro-elem"><span class="chordpro-chord">' . $chord . '</span><span class="chordpro-text">' . $text . '</span></span>';
        }

        $verse .= '</div>';

        return $verse;
    }

    private function getLyricsOnlyHtml(Lyrics $lyrics): string
    {
        $verse = '<div class="chordpro-verse">';

        foreach ($lyrics->getBlocks() as $block) {
            $verse .= ltrim($block->getText());
        }

        $verse .= '</div>';

        return $verse;
    }
}

