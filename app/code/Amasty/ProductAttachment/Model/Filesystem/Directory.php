<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ProductAttachment
 */


namespace Amasty\ProductAttachment\Model\Filesystem;

class Directory
{
    const AMFILE_DIRECTORY = 'amasty' . DIRECTORY_SEPARATOR . 'amfile' . DIRECTORY_SEPARATOR;

    const ATTACHMENT = 'attachment';

    const ICON = 'icon';

    const TMP_DIRECTORY = 'tmp';

    const IMPORT = 'import';

    const IMPORT_FTP = 'ftp';

    const DIRECTORY_CODES = [
        self::ATTACHMENT => self::AMFILE_DIRECTORY . 'attach',
        self::ICON => self::AMFILE_DIRECTORY . 'icon',
        self::TMP_DIRECTORY => self::AMFILE_DIRECTORY . 'tmp',
        self::IMPORT => self::AMFILE_DIRECTORY . 'import',
        self::IMPORT_FTP => self::AMFILE_DIRECTORY . 'import' . DIRECTORY_SEPARATOR . 'ftp'
    ];
}
