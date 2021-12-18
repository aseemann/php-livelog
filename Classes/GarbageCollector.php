<?php

namespace ASeemann\PhpLiveLog;

/**
 * Class GarbageCollector
 *
 * @package ASeemann\PhpLiveLog
 * @author  Axel Seemann <kummeraxel@gmail.com>
 * @licence AGPL-v3
 * @link    https://github.com/aseemann
 *
 */
class GarbageCollector
{
    /**
     * @return void
     */
    public function removeOutdatedFiles(): void
    {
        $list = $this->getDeleteCandidates();

        foreach ($list as $file) {
            unlink($file);
        }
    }

    /**
     * Returns the file wich are last modified before one hour
     *
     * @return array<string>
     */
    public function getDeleteCandidates(): array
    {
        $list = array_merge(
            glob(sprintf(Constants::LOG_FILE_PATH_PATTERN, '*')) ?? [],
            glob(sprintf(Constants::STATE_FILE_PATH_PATTERN, '*')) ?? []
        );

        if (empty($list)) {
            return [];
        }

        foreach ($list as $key => $file) {
            if (filemtime($file) > (time() - 3600)) {
                unset($list[$key]);
            }
        }

        return $list;
    }
}
