<?php
namespace wapmorgan\FileTypeDetector;

class Detector {
    const AUDIO = 'audio';
    const VIDEO = 'video';
    const IMAGE = 'image';
    const ARCHIVE = 'archive';
    const DISK_IMAGE = 'disk_image';
    const DATABASE = 'database';
    const DOCUMENT = 'document';
    const FONT = 'font';
    const APPLICATION = 'application';
    const PRESENTATION = 'presentation';
    const SPREADSHEET = 'spreadsheet';
    const FEED = 'feed';
    const SCENARIO = 'scenario';

    const JPEG = 'jpeg';
    const BMP = 'bmp';
    const GIF = 'gif';
    const PNG = 'png';
    const TIFF = 'tiff';
    const PSD = 'psd';
    const ICO = 'ico';

    const ARJ = 'arj';
    const BZIP2 = 'bzip2';
    const GZIP = 'gzip';
    const LZMA2 = 'lzma2';
    const _7ZIP = '7z';
    const CAB = 'cab';
    const JAR = 'jar';
    const RAR = 'rar';
    const TAR = 'tar';
    const ZIP = 'zip';
    const ARC = 'arc';
    const DAR = 'dar';

    const ISO = 'iso';
    const NRG = 'nrg';
    const VHD = 'vhd';

    const ACCDB = 'accdb';
    const MDB = 'mdb';
    const ODB = 'odb';
    const SQLITE = 'sqlite';

    const DOC = 'doc';
    const DOCX = 'docx';
    const HTML = 'html';
    const ODT = 'odt';
    const PDF = 'pdf';
    const RTF = 'rtf';
    const TXT = 'txt';
    const XML = 'xml';
    const MARKDOWN = 'markdown';
    const JSON = 'json';
    const YAML = 'yaml';

    const ATOM = 'atom';
    const RSS = 'rss';

    const OTF = 'otf';
    const TTF = 'ttf';

    const APK = 'apk';
    const COM = 'com';
    const EXE = 'exe';
    const XAP = 'xap';

    const PPT = 'ppt';
    const PPTX = 'pptx';
    const ODP = 'odp';

    const FLAC = 'flac';
    const WMA = 'wma';
    const AMR = 'amr';
    const MP3 = 'mp3';
    const AAC = 'aac';
    const M3U = 'm3u';
    const OGG = 'ogg';
    const WAV = 'wav';
    const MIDI = 'midi';

    const ODS = 'ods';
    const XLS = 'xls';
    const XLSX = 'xlsx';
    const CSV = 'csv';
    const TSV = 'tsv';

    const _3GP = '3gp';
    const ASF = 'asf';
    const AVI = 'avi';
    const FLV = 'flv';
    const M4V = 'm4v';
    const MKV = 'mkv';
    const MOV = 'mov';
    const MPEG = 'mpeg';
    const MP4 = 'mp4';
    const SWF = 'swf';
    const VOB = 'vob';
    const WMV = 'wmv';
    const WEBM = 'webm';

    const REG = 'reg';

    static protected $aliases = array(
        'jpg' => self::JPEG,
        'tif' => self::TIFF,
        'mpg' => self::MPEG,
        'mpe' => self::MPEG,
        'm4a' => self::AAC,
        'yml' => self::YAML,
        'md' => self::MARKDOWN,
        'mid' => self::MIDI,
    );

    static protected $extensions = array(
        'jpeg' => self::JPEG,
        'bmp' => self::BMP,
        'gif' => self::GIF,
        'png' => self::PNG,
        'tiff' => self::TIFF,
        'psd' => self::PSD,
        'ico' => self::ICO,
        'arj' => self::ARJ,
        'bz2' => self::BZIP2,
        'gz' => self::GZIP,
        'xz' => self::LZMA2,
        '7z' => self::_7ZIP,
        'cab' => self::CAB,
        'jar' => self::JAR,
        'rar' => self::RAR,
        'tar' => self::TAR,
        'zip' => self::ZIP,
        'arc' => self::ARC,
        'dar' => self::DAR,
        'iso' => self::ISO,
        'nrg' => self::NRG,
        'vhd' => self::VHD,
        'accdb' => self::ACCDB,
        'mdb' => self::MDB,
        'odb' => self::ODB,
        'doc' => self::DOC,
        'docx' => self::DOCX,
        'html' => self::HTML,
        'odt' => self::ODT,
        'pdf' => self::PDF,
        'rtf' => self::RTF,
        'txt' => self::TXT,
        'md' => self::MARKDOWN,
        'json' => self::JSON,
        'yaml' => self::YAML,
        'xml' => self::XML,
        'atom' => self::ATOM,
        'rss' => self::RSS,
        'otf' => self::OTF,
        'ttf' => self::TTF,
        'apk' => self::APK,
        'com' => self::COM,
        'exe' => self::EXE,
        'xap' => self::XAP,
        'ppt' => self::PPT,
        'pptx' => self::PPTX,
        'odp' => self::ODP,
        'flac' => self::FLAC,
        'wma' => self::WMA,
        'amr' => self::AMR,
        'mp3' => self::MP3,
        'aac' => self::AAC,
        'm3u' => self::M3U,
        'ogg' => self::OGG,
        'wav' => self::WAV,
        'midi' => self::MIDI,
        'ods' => self::ODS,
        'xls' => self::XLS,
        'xlsx' => self::XLSX,
        'csv' => self::CSV,
        'tsv' => self::TSV,
        '3gp' => self::_3GP,
        'asf' => self::ASF,
        'avi' => self::AVI,
        'flv' => self::FLV,
        'm4v' => self::M4V,
        'mkv' => self::MKV,
        'mov' => self::MOV,
        'mpeg' => self::MPEG,
        'mp4' => self::MP4,
        'swf' => self::SWF,
        'vob' => self::VOB,
        'wmv' => self::WMV,
        'webm' => self::WEBM,
        'reg' => self::REG,
    );

    static protected $types = array(
        self::IMAGE => array(
            self::JPEG,
            self::BMP,
            self::GIF,
            self::PNG,
            self::TIFF,
            self::PSD,
            self::ICO,
        ),

        self::ARCHIVE => array(
            self::ARJ,
            self::BZIP2,
            self::GZIP,
            self::LZMA2,
            self::_7ZIP,
            self::CAB,
            self::JAR,
            self::RAR,
            self::TAR,
            self::ZIP,
            self::ARC,
            self::DAR,
        ),

        self::DISK_IMAGE => array(
            self::ISO,
            self::NRG,
            self::VHD,
        ),

        self::DATABASE => array(
            self::ACCDB,
            self::MDB,
            self::ODB,
            self::SQLITE,
        ),

        self::DOCUMENT => array(
            self::DOC,
            self::DOCX,
            self::HTML,
            self::ODT,
            self::PDF,
            self::RTF,
            self::TXT,
            self::MARKDOWN,
            self::JSON,
            self::YAML,
            self::XML,
        ),

        self::FEED => array(
            self::ATOM,
            self::RSS,
        ),

        self::FONT => array(
            self::OTF,
            self::TTF,
        ),

        self::APPLICATION => array(
            self::APK,
            self::COM,
            self::EXE,
            self::XAP,
        ),

        self::PRESENTATION => array(
            self::PPT,
            self::PPTX,
            self::ODP,
        ),

        self::AUDIO => array(
            self::FLAC,
            self::WMA,
            self::AMR,
            self::MP3,
            self::AAC,
            self::M3U,
            self::OGG,
            self::WAV,
            self::MIDI,
        ),

        self::SPREADSHEET => array(
            self::ODS,
            self::XLS,
            self::XLSX,
            self::CSV,
            self::TSV,
        ),

        self::VIDEO => array(
            self::_3GP,
            self::ASF,
            self::AVI,
            self::FLV,
            self::M4V,
            self::MKV,
            self::MOV,
            self::MPEG,
            self::MP4,
            self::SWF,
            self::VOB,
            self::WMV,
            self::WEBM,
        ),

        self::SCENARIO => array(
            self::REG,
        ),
    );

    static protected $mimeTypes = array(
        self::JPEG => 'image/jpeg',
        self::BMP => 'image/bmp',
        self::GIF => 'image/gif',
        self::PNG => 'image/png',
        self::TIFF => 'image/tiff',
        self::PSD => 'image/vnd.adobe.photoshop',
        self::ICO => 'image/x-icon',

        self::ARJ => 'application/arj',
        self::BZIP2 => 'application/x-bzip2',
        self::GZIP => 'application/gzip',
        self::_7ZIP => 'application/x-7z-compressed',
        self::LZMA2 => 'application/x-xz',
        self::CAB => 'application/vnd.ms-cab-compressed',
        self::JAR => 'application/java-archive',
        self::RAR => 'application/x-rar-compressed',
        self::TAR => 'application/x-tar',
        self::ZIP => 'application/zip',
        self::ARC => 'application/x-freearc',
        self::DAR => 'application/x-dar',

        self::ISO => 'application/x-iso9660-image',

        self::ACCDB => 'application/x-msaccess',
        self::MDB => 'application/x-msaccess',
        self::ODB => 'application/vnd.oasis.opendocument.database',
        self::SQLITE => 'application/x-sqlite3',

        self::DOC => 'application/msword',
        self::DOCX => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        self::HTML => 'text/html',
        self::ODT => 'application/vnd.oasis.opendocument.text',
        self::PDF => 'application/pdf',
        self::RTF => 'application/rtf',
        self::TXT => 'text/plain',
        self::MARKDOWN => 'text/markdown',
        self::YAML => 'text/yaml',
        self::JSON => 'application/json',
        self::XML => 'application/xml',

        self::ATOM => 'application/atom+xml',
        self::RSS => 'application/rss+xml',

        self::OTF => 'application/x-font-otf',
        self::TTF => 'application/x-font-ttf',

        self::APK => 'application/vnd.android.package-archive',
        self::COM => 'application/x-msdownload',
        self::EXE => 'application/x-msdownload',
        self::XAP => 'application/x-silverlight-app',

        self::PPT => 'application/vnd.ms-powerpoint',
        self::PPTX => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        self::ODP => 'application/vnd.oasis.opendocument.presentation',

        self::FLAC => 'audio/x-flac',
        self::WMA => 'audio/x-ms-wma',
        self::AMR => 'audio/amr',
        self::MP3 => 'audio/mpeg',
        self::AAC => 'audio/x-aac',
        self::M3U => 'audio/x-mpegurl',
        self::OGG => 'audio/ogg',
        self::WAV => 'audio/x-wav',
        self::MIDI => 'audio/midi',

        self::ODS => 'application/vnd.oasis.opendocument.spreadsheet',
        self::XLS => 'application/vnd.ms-excel',
        self::XLSX => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        self::CSV => 'text/csv',
        self::TSV => 'text/tab-separated-values',

        self::_3GP => 'video/3gpp',
        self::AVI => 'video/x-msvideo',
        self::FLV => 'video/x-flv',
        self::M4V => 'video/x-m4v',
        self::MKV => 'video/x-matroska',
        self::MOV => 'video/quicktime',
        self::MPEG => 'video/mpeg',
        self::MP4 => 'video/mp4',
        self::SWF => 'application/x-shockwave-flash',
        self::VOB => 'video/x-ms-vob',
        self::WMV => 'video/x-ms-wmv',
        self::WEBM => 'video/webm',

        self::REG => 'text/plain',
    );

    static protected $signatures = [
        // Images signatures
        self::JPEG => [[0 => [0xFF, 0xD8, 0xFF, 0xE0]]],
        self::BMP => [[0 => [0x42, 0x4D]]],
        self::GIF => [
            [0 => [0x47, 0x49, 0x46, 0x38, 0x37, 0x61]],
            // or
            [0 => [0x47, 0x49, 0x46, 0x38, 0x39, 0x61]]
        ],
        self::PNG => [[0 => [0x89, 0x50, 0x4E, 0x47, 0x0D, 0x0A, 0x1A, 0x0A]]],
        self::TIFF => [
            [0 => [0x49, 0x20, 0x49]],
            // or
            [0 => [0x49, 0x49, 0x2A, 0x00]],
            // or
            [0 => [0x4D, 0x4D, 0x00, 0x2A]],
            // or
            [0 => [0x4D, 0x4D, 0x00, 0x2B]]
        ],
        self::PSD => [[0 => [0x38, 0x42, 0x50, 0x53]]],
        self::ICO => [[0 => [0x00, 0x00, 0x01, 0x00]]],

        // Archives signatures
        self::ARJ => [[0 => [0x60, 0xEA]]],
        self::BZIP2 => [[0 => [0x42, 0x5A, 0x68]]],
        self::GZIP => [[0 => [0x1F, 0x8B]]],
        self::_7ZIP => [[0 => [0x37, 0x7A, 0xBC, 0xAF, 0x27, 0x1C]]],
        self::CAB => [[0 => [0x4D, 0x53, 0x43, 0x46]]],
        self::JAR => [
            [0 => [0x50, 0x4B, 0x03, 0x04, 0x14, 0x00, 0x08, 0x00, 0x08, 0x00]],
            // or
            [0 => [0x5F, 0x27, 0xA8, 0x89]]
        ],
        self::RAR => [
            [0 => [0x52, 0x61, 0x72, 0x21, 0x1A, 0x07, 0x00]],
            // or
            [0 => [0x52, 0x61, 0x72, 0x21, 0x1A, 0x07, 0x01, 0x00]]
        ],
        self::TAR => [
            [0 => [0x75, 0x73, 0x74, 0x61, 0x72, 0x00, 0x30, 0x30]],
            // or
            [0 => [0x75, 0x73, 0x74, 0x61, 0x72, 0x20, 0x20, 0x00]]
        ],
        self::ARC => [[0 => [0x41, 0x72, 0x43, 0x01]]],
        self::DAR => [[0 => [0x00, 0x00, 0x00, 0x7B]]],

        // Disk images signatures
        self::ISO => [[0 => [0x43, 0x44, 0x30, 0x30, 0x31]]],
        self::NRG => [
            [-8 => ['N', 'E', 'R', 'O']],
            // or
            [-12 => ['N', 'E', 'R', '5']]
        ],

        // Spreadsheets signatures
        self::ACCDB => [[0 => [0x00, 0x01, 0x00, 0x00, 0x53, 0x74, 0x61, 0x6E, 0x64, 0x61, 0x72, 0x64, 0x20, 0x41, 0x43, 0x45, 0x20, 0x44, 0x42]]],
        self::MDB => [[0 => [0x00, 0x01, 0x00, 0x00, 0x53, 0x74, 0x61, 0x6E, 0x64, 0x61, 0x72, 0x64, 0x20, 0x4A, 0x65, 0x74, 0x20, 0x44, 0x42]]],
        self::SQLITE => [[0 => [0x53, 0x51, 0x4C, 0x69, 0x74, 0x65, 0x20, 0x66, 0x6F, 0x72, 0x6D, 0x61, 0x74, 0x20, 0x33, 0x00]]],

        // Microsoft Office old formats (doc, xls, ppt)
        self::DOC => [
            [
                0 => [0xD0, 0xCF, 0x11, 0xE0, 0xA1, 0xB1, 0x1A, 0xE1],
                // and
                512 => [0xEC, 0xA5, 0xC1, 0x00],
            ]
        ],
        self::XLS => [
            [
                0 => [0xD0, 0xCF, 0x11, 0xE0, 0xA1, 0xB1, 0x1A, 0xE1],
                // and
                512 => [0x09, 0x08, 0x10, 0x00, 0x00, 0x06, 0x05, 0x00],
            ]
        ],
        self::PPT => [
            [
                0 => [0xD0, 0xCF, 0x11, 0xE0, 0xA1, 0xB1, 0x1A, 0xE1],
                // and
                512 => [0xA0, 0x46, 0x1D, 0xF0]
            ],
            // or
            [
                0 => [0xD0, 0xCF, 0x11, 0xE0, 0xA1, 0xB1, 0x1A, 0xE1],
                // and
                512 => [0x00, 0x6E, 0x1E, 0xF0]
            ],
            // or
            [
                0 => [0xD0, 0xCF, 0x11, 0xE0, 0xA1, 0xB1, 0x1A, 0xE1],
                // and
                512 => [0x0F, 0x00, 0xE8, 0x03]
            ]
        ],

        // Microsoft Office new formats (docx, xlsx, pptx)
        self::DOCX => [
            [
                0 => [0x50, 0x4B, 0x03, 0x04, 0x14, 0x00, 0x06, 0x00],
                // and
                // search for substring at the end of file
                -22 => [
                    'bytes' => ['w', 'o', 'r', 'd', '/'],
                    'depth' => 512,
                    'reverse' => true
                ]
            ]
        ],
        self::XLSX => [
            [
                0 => [0x50, 0x4B, 0x03, 0x04, 0x14, 0x00, 0x06, 0x00],
                // and
                // search for substring at the end of file
                -22 => [
                    'bytes' => ['x', 'l', '/'],
                    'depth' => 512,
                    'reverse' => true
                ]
            ]
        ],
        self::PPTX => [
            [
                0 => [0x50, 0x4B, 0x03, 0x04, 0x14, 0x00, 0x06, 0x00],
                // and
                // search for substring at the end of file
                -22 => [
                    'bytes' => ['p', 'p', 't', '/'],
                    'depth' => 512,
                    'reverse' => true
                ]
            ]
        ],

        // Open Alliance formats
        self::ODT => [
            [
                0 => [0x50, 0x4B, 0x03, 0x04],
                // and
                30 => ['m', 'i', 'm', 'e',  't',  'y',  'p',  'e', 'a', 'p', 'p', 'l', 'i', 'c', 'a', 't', 'i', 'o', 'n', '/', 'v', 'n', 'd', '.', 'o', 'a', 's', 'i', 's', '.', 'o', 'p', 'e', 'n', 'd', 'o', 'c', 'u', 'm', 'e', 'n', 't', '.'],
                // and
                73 => ['t', 'e', 'x', 't'],
            ]
        ],
        self::ODS => [
            [
                0 => [0x50, 0x4B, 0x03, 0x04],
                // and
                30 => ['m', 'i', 'm', 'e',  't',  'y',  'p',  'e', 'a', 'p', 'p', 'l', 'i', 'c', 'a', 't', 'i', 'o', 'n', '/', 'v', 'n', 'd', '.', 'o', 'a', 's', 'i', 's', '.', 'o', 'p', 'e', 'n', 'd', 'o', 'c', 'u', 'm', 'e', 'n', 't', '.'],
                // and
                73 => ['s', 'p', 'r', 'e', 'a', 'd', 's', 'h', 'e', 'e', 't'],
            ]
        ],
        self::ODP => [
            [
                0 => [0x50, 0x4B, 0x03, 0x04],
                // and
                30 => ['m', 'i', 'm', 'e',  't',  'y',  'p',  'e', 'a', 'p', 'p', 'l', 'i', 'c', 'a', 't', 'i', 'o', 'n', '/', 'v', 'n', 'd', '.', 'o', 'a', 's', 'i', 's', '.', 'o', 'p', 'e', 'n', 'd', 'o', 'c', 'u', 'm', 'e', 'n', 't', '.'],
                // and
                73 => ['p', 'r', 'e', 's', 'e', 'n', 't', 'a', 't', 'i', 'o', 'n'],
            ]
        ],
        self::ODB => [
            [
                0 => [0x50, 0x4B, 0x03, 0x04],
                // and
                30 => ['m', 'i', 'm', 'e',  't',  'y',  'p',  'e', 'a', 'p', 'p', 'l', 'i', 'c', 'a', 't', 'i', 'o', 'n', '/', 'v', 'n', 'd', '.', 'o', 'a', 's', 'i', 's', '.', 'o', 'p', 'e', 'n', 'd', 'o', 'c', 'u', 'm', 'e', 'n', 't', '.'],
                // and
                73 => ['b', 'a', 's', 'e'],
            ]
        ],

        // Text formats
        self::HTML => [[0 => '<html']],
        self::PDF => [[0 => [0x25, 0x50, 0x44, 0x46]]],
        self::RTF => [[0 => [0x7B, 0x5C, 0x72, 0x74, 0x66, 0x31]]],
        self::ATOM => [[
            0 => '<?xml',
            // and
            // search for substring "Atom" in the second xml tag
            10 => [
                'bytes' => ['A', 't', 'o', 'm'],
                'depth' => 100
            ]
        ]],
        self::RSS => [[
            0 => '<?xml',
            // search for substring "<rss" at the start of file
            10 => [
                'bytes' => ['<', 'r', 's', 's'],
                'depth' => 100
            ]
        ]],
        // make sure xml at the end of Text's section
        self::XML => [[0 => '<?xml']],

        // Font formats
        self::OTF => [[0 => [0x4F, 0x54, 0x54, 0x4F]]],
        self::TTF => [[0 => [0x00, 0x01, 0x00, 0x00, 0x00]]],

        // Executables formats
        self::APK => [[
            0 => [0x50, 0x4B, 0x03, 0x04],
            // and
            30 => ['A', 'n', 'd', 'r', 'o', 'i', 'd', 'M', 'a', 'n', 'i', 'f', 'e', 's', 't', '.', 'x', 'm', 'l'],
        ]],
        self::EXE => [[0 => [0x4D, 0x5A]]],

        // Audios formats
        self::FLAC => [[0 => [0x66, 0x4C, 0x61, 0x43, 0x00, 0x00, 0x00, 0x22]]],
        self::AMR => [[0 => [0x23, 0x21, 0x41, 0x4D, 0x52]]],
        self::MP3 => [[0 => [0x49, 0x44, 0x33]]],
        self::AAC => [
            [0 => [0xFF, 0xF1]],
            // or
            [0 => [0xFF, 0xF9]]
        ],
        self::M3U => [[0 => ['#', 'E', 'X', 'T', 'M', '3', 'U']]],
        self::OGG => [[0 => ['O', 'g', 'g', 'S']]],
        self::MIDI => [[0 => [0x4D, 0x54, 0x68, 0x64]]],

        self::_3GP => [[0 => [0x00, 0x00, 0x00, 0x14, 0x66, 0x74, 0x79, 0x70, 0x33, 0x67, 0x70]]],
        self::AVI => [[
            0 => [0x52, 0x49, 0x46, 0x46],
            // and
            8 => [0x41, 0x56, 0x49, 0x20, 0x4C, 0x49, 0x53, 0x54]
        ]],
        self::FLV => [[0 => [0x46, 0x4C, 0x56, 0x01]]],
        self::M4V => [[0 => [0x00, 0x00, 0x00, 0x18, 0x66, 0x74, 0x79, 0x70, 0x6D, 0x70, 0x34, 0x32]]],
        self::MKV => [[0 => [0x1A, 0x45, 0xDF, 0xA3, 0x93, 0x42, 0x82, 0x88, 0x6D, 0x61, 0x74, 0x72, 0x6F, 0x73, 0x6B, 0x61]]],
        self::MOV => [
            [4 => [0x66, 0x74, 0x79, 0x70, 0x71, 0x74, 0x20, 0x20]],
            // or
            [4 => [0x6D, 0x6F, 0x6F, 0x76]]
        ],
        self::MP4 => [
            [4 => [0x66, 0x74, 0x79, 0x70, 0x69, 0x73, 0x6F, 0x6D]],
            // or
            [4 => [0x66, 0x74, 0x79, 0x70, 0x33, 0x67, 0x70, 0x35]],
            // or
            [4 => [0x66, 0x74, 0x79, 0x70, 0x4D, 0x53, 0x4E, 0x56]],
            // or
            [4 => [0x66, 0x74, 0x79, 0x70, 0x4D, 0x34, 0x41, 0x20]]
        ],
        self::MPEG => [[
            0 => [0x00, 0x00, 0x01],
            // and
            -4 => [0x00, 0x00, 0x01, 0xB7]
        ]],
        self::SWF => [[0 => [0x5A, 0x57, 0x53]]],
        self::VOB => [[
            0 => [0x00, 0x00, 0x01, 0xBA],
            // and
            -4 => [0x00, 0x00, 0x01, 0xB9]
        ]],
        self::WEBM => [[0 => [0x1A, 0x45, 0xDF, 0xA3]]],

        // zip is a container for a lot of formats
        self::ZIP => [
            [0 => [0x50, 0x4B, 0x03, 0x04]],
            // or
            [0 => [0x50, 0x4B, 0x05, 0x06]],
            // or
            [0 => [0x50, 0x4B, 0x07, 0x08]]
        ],

        // Scneraios formats
        self::REG => [
            [0 => [0xFF, 0xFE]],
            // or
            [0 => [0x52, 0x45, 0x47, 0x45, 0x44, 0x49, 0x54]]
        ]
    ];

    static public function detectByFilename($filename) {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (isset(self::$aliases[$ext])) $ext = self::$aliases[$ext];
        if (isset(self::$extensions[$ext])) {
            $format = array(null, self::$extensions[$ext]);
            foreach (self::$types as $type => $formats) {
                if (in_array($format[1], $formats)) {
                    $format[0] = $type;
                    break;
                }
            }
            $format[2] = isset(self::$mimeTypes[$format[1]]) ? self::$mimeTypes[$format[1]] : false;
            return $format;
        }
        return false;
    }

    static public function detectByContent($source) {
        $stream = new ContentStream($source);
        foreach (self::$signatures as $format => $signatures) {
            foreach ($signatures as $or_signature) {
                $passed = true;
                foreach ($or_signature as $offset => $and_signature) {
                    // search for substring in range
                    if (isset($and_signature['bytes'])) {
                        if ($stream->find($offset, $and_signature['bytes'],
                            isset($and_signature['depth']) ? $and_signature['depth'] : 512,
                            isset($and_signature['reverse']) ? $and_signature['reverse'] : false
                            ) === false) {
                            $passed = false;
                            break;
                        }
                    }
                    // exact match
                    else {
                        if ($stream->checkBytes($offset, $and_signature) === false) {
                            $passed = false;
                            break;
                        }
                    }
                }
                // if earlier we did not break inner loop, then all signatures matched
                if ($passed) {
                    $format = array(null, $format);
                    foreach (self::$types as $type => $formats) {
                        if (in_array($format[1], $formats)) {
                            $format[0] = $type;
                            break;
                        }
                    }
                    $format[2] = isset(self::$mimeTypes[$format[1]]) ? self::$mimeTypes[$format[1]] : false;
                    return $format;
                }
            }
        }
        return false;
    }

    static public function getMimeType($file) {
        $format = self::detectByFilename($file) ?: self::detectByContent($file);
        if ($format === false)
            return false;
        return $format[2];
    }
}
