<?php

namespace ChordPro;

class JSONFormatter extends Formatter implements FormatterInterface {

    public function format(Song $song, array $options): string
    {
        $json = [];

        $this->setOptions($song,$options);

        foreach ($song->lines as $line) {
            if (null === $line) {
                $json[] = null;
                continue;
            }

            $json[] = $this->getLineJSON($line);
        }
        return json_encode($json,JSON_PRETTY_PRINT);
    }


    private function getLineJSON(Line $line)
    {
        if ($line instanceof Metadata) {
            return $this->getMetadataJSON($line);
        }

        if ($line instanceof Lyrics) {
            return (true === $this->hasNoChords) ? $this->getLyricsOnlyJSON($line) : $this->getLyricsJSON($line);
        }
    }

    private function getMetadataJSON($metadata)
    {
        if (empty($metadata->getValue())) {
            return [$metadata->getName()];
        }
        else {
            switch($metadata->getName()) {
                default:
                    return [$metadata->getName() => $metadata->getValue()];
                    break;
            }
        }
    }
    private function getLyricsJSON($lyrics)
    {
        $return = [];

        foreach ($lyrics->getBlocks() as $block) {
            $chord = (true === $this->hasFrenchChords) ? $block->getFrenchChord() : $block->getChord();

            if (is_array($chord)) {
                // Implode all !
                $chord = implode('/',array_map("implode",$chord)).' ';
            }

            $text = $block->getText();
            $return[] = array('chord' => trim($chord), 'text' => $text);
        }
        return $return;
    }
    private function getLyricsOnlyJSON($lyrics)
    {
        $return = '';

        foreach ($lyrics->getBlocks() as $block) {
            $return .= ltrim($block->getText());
        }
        return $return;
    }
}


