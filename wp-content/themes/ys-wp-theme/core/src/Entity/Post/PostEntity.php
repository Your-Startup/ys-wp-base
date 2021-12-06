<?php
namespace YS\Core\Entity\Post;

//use YS\Core\Repository\News\CategoriesRepository;
//use YS\Core\Repository\Users\UsersRepository;
//use YS\Core\Repository\Tags\TagsRepository;
//use YS\Core\Repository\Media\MediaRepository;
//use YS\Core\Service\NewsService;
//use YS\Core\Util\DateUtil;
use YS\Core\Entity\AbstractEntity;
use YS\Core\Repository\Media\MediaRepository;
use YS\Core\Util\SeoUtil;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method setThumbnailId($value)
 */
class PostEntity extends AbstractEntity
{
    protected int    $id;
    protected string $title;
    protected string $content;
    protected string $status;
    protected string $slug;
    protected string $excerpt;
    protected ?int   $authorId;
    //protected ?array $author;
    protected int    $publishedAt;
    protected int    $updatedAt;
    protected ?int   $thumbnailId;
    /**
     * @param $thumbnailId
     */
    protected ?array $thumbnail;
    //protected ?array $categories;
    //protected ?array $tags;
    protected int    $commentsCount;
    protected bool   $isCommentsOpen;
    protected string $uri;

   // private CategoriesRepository $categoriesRepo;
    //private TagsRepository       $tagsRepo;
    //private UsersRepository      $authorRepo;
   // private MediaRepository      $mediaRepo;
    //private NewsService          $newsService;

   // protected $guarded  = ['author', 'attachment', 'categories', 'tags'];
   // protected $hidden   = ['authorId', 'attachmentId', 'primaryCatId'];
    protected array $lazyLoad = ['author', 'attachment', 'categories', 'tags', 'seoTitle', 'seoDescription', 'seoImage'];

    const COLUMNS_DEFAULT_FIELDS_MAP = [
        'ID'                => 'id',
        'post_title'        => 'title',
        'post_content'      => 'content',
        'post_status'       => 'status',
        'post_name'         => 'slug',
        'post_excerpt'      => 'lead',
        'post_author'       => 'authorId',
        'comment_status'    => 'isCommentsOpen',
        'comment_count'     => 'commentsCount',
        'post_date_gmt'     => 'publishedAt',
        'post_modified_gmt' => 'updatedAt',
    ];

    private MediaRepository $mediaRepo;

    public function __construct()
    {
        parent::__construct();

        // Repositories
        //$this->authorRepo     = new UsersRepository();
        //$this->categoriesRepo = new CategoriesRepository();
        //$this->tagsRepo       = new TagsRepository();
        $this->mediaRepo      = new MediaRepository();
       // $this->newsService    = new NewsService();

        $this->setColumnsMap([
            '_thumbnail_id' => 'thumbnailId',
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function setDefaultValues()
    {
        $this->status      = 'publish';
        $this->publishedAt = time();
        $this->updatedAt   = time();
        $this->type        = 'article';
    }

    /**
     * @inheritDoc
     */
    public static function setValidationConstraints(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('title', new Assert\NotBlank([
            'message' => 'Поле `title` не может быть пустым!',
        ]));
    }

    public function getThumbnail(): array
    {
        if (isset($this->thumbnail)) {
            return $this->thumbnail;
        }

        if (empty($this->thumbnailId)) {
            return [];
        }

        return $this->thumbnail = $this->mediaRepo->find($this->thumbnailId, ['uri']);
    }

    public function getCategories()
    {

    }

    public function getTags()
    {

    }

    public function getAuthor()
    {

    }

    public function getType(): string
    {

    }

    public function getUri()
    {
        return get_permalink($this->id);
    }

    /**
     * @param bool|string $status
     */
    public function setIsCommentsOpen($status)
    {
        $status = $status === true || $status === 'open';
        $this->isCommentsOpen = $status;
    }

    public function getSeoTitle()
    {
        return SeoUtil::getPostSeoTitle($this);
    }

    public function getSeoDescription()
    {
        return SeoUtil::getPostSeoDescription($this);
    }


    public function getSeoImage()
    {
        //return SeoUtil::getSeoImage($this->id, $this->attachment['full_uri']);
    }
}
