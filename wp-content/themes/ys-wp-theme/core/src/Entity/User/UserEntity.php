<?php

namespace YS\Core\Entity\User;

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

class UserEntity extends AbstractEntity
{
    protected int    $id;
    protected string $login;
    protected string $password;
    protected string $displayName;
    protected string $firstName;
    protected string $lastName;
    protected string $phone;
    //protected string $niceName;
    protected string $email;
    //protected string $url;
    protected int    $registeredAt;
    protected string $capabilities;
    /**
     * @param $capabilities
     */
    protected array $roles;
    //protected int  $activationKey;
    //protected ?int $status;
    //protected ?int $displayName;

    const COLUMNS_DEFAULT_FIELDS_MAP = [
        'ID'                  => 'id',
        'user_login'          => 'login',
        'user_pass'           => 'password',
        'user_nicename'       => 'niceName',
        'user_email'          => 'email',
        'user_url'            => 'url',
        'user_registered'     => 'registeredAt',
        'user_activation_key' => 'activationKey',
        'user_status'         => 'status',
        'display_name'        => 'displayName',
    ];
    private MediaRepository $mediaRepo;

    public function __construct()
    {
        parent::__construct();

        // Repositories
        //$this->authorRepo     = new UsersRepository();
        //$this->categoriesRepo = new CategoriesRepository();
        //$this->tagsRepo       = new TagsRepository();
        $this->mediaRepo = new MediaRepository();
        // $this->newsService    = new NewsService();

        $this->setColumnsMap([
            'wp_capabilities' => 'capabilities',
            'first_name'      => 'firstName',
            'last_name'       => 'lastName',
            'user_phone'      => 'phone',
        ]);
    }
}
