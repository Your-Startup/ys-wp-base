<?php

namespace YS\Core\Entity\Media;

use YS\Core\Entity\AbstractEntity;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class MediaEntity extends AbstractEntity
{
    protected int     $id;
    protected string  $title;
    protected string  $uri;
    //protected ?string $thumbnailUri;
    //protected ?string $customSizeUri;
    //protected ?string $fullUri;
    protected string  $alt;
    protected string  $caption;
    protected string  $description;
    protected ?int    $date;
    protected string  $mimeType;
    protected string  $source;
    protected ?string $meta;
    //protected ?array  $size;
    protected int     $authorId;
    /** @var array */
    protected $file;
    //protected $hidden   = ['file', 'uri', 'meta', 'size'];
    //protected $guarded  = ['thumbnailUri', 'customSizeUri', 'fullUri'];
    //protected $lazyLoad = ['thumbnailUri', 'customSizeUri', 'fullUri'];

    const COLUMNS_DEFAULT_FIELDS_MAP = [
        'ID'             => 'id',
        'post_title'     => 'title',
        'guid'           => 'uri',
        'post_excerpt'   => 'caption',
        'post_content'   => 'description',
        'post_author'    => 'authorId',
        'post_parent'    => 'postId',
        'post_date_gmt'  => 'date',
        'post_mime_type' => 'mimeType',
    ];

    public function __construct()
    {
        parent::__construct();

        $this->setColumnsMap([
            '_wp_attachment_image_alt' => 'alt',
            '_image_signature'         => 'source',
            '_wp_attachment_metadata'  => 'meta',
        ]);
    }
    // TODO: 30.11.20 vadeemch81 / Установить правила валидации, используемые при загрузке картинок
    //  https://symfony.com/doc/current/reference/constraints/Image.html
    //  https://symfony.com/doc/current/reference/constraints/File.html
    public static function setValidationConstraints(ClassMetadata $metadata)
    {
    }

    public function getFullUri()
    {
        if (isset($this->fullUri)) {
            return $this->fullUri;
        }

        return $this->fullUri = $this->uri;
    }

    public function getThumbnailUri()
    {
        if (isset($this->thumbnailUri)) {
            return $this->thumbnailUri;
        }

        return $this->thumbnailUri = $this->uri;
    }

    public function getCustomSizeUri()
    {
        if (isset($this->customSizeUri)) {
            return $this->customSizeUri;
        }

        if (!isset($this->size)) {
            return '';
        }

        return $this->customSizeUri = $this->photonUri($this->uri, $this->size);
    }

    public function getSizeParams(string $sizeName)
    {
        if (empty($this->meta)) {
            return [];
        }

        $meta = unserialize($this->meta);
        if (empty($meta['sizes'][$sizeName]['width']) && empty($meta['sizes'][$sizeName]['height'])) {
            return [];
        }

        return [
            $meta['sizes'][$sizeName]['width'],
            $meta['sizes'][$sizeName]['height']
        ];
    }

    private function photonUri(?string $url, array $size = [])
    {
        if (empty($url) || !function_exists('jetpack_photon_url')) {
            return $url;
        }

        $args = [];

        if (isset($size[0])) {
            $args['w'] = $size[0];
        }

        if (isset($size[1])) {
            $args['h'] = $size[1];
        }

        return jetpack_photon_url($url, $args);
    }

    protected function setDefaultValues()
    {
    }
}
