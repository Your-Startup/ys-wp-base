<?php
namespace YS\Site\Entity\Poems;

use YS\Core\Entity\Post\PostEntity;
use YS\Core\Repository\Term\TermRepository;

class PoemsEntity extends PostEntity
{
    protected ?int $price;

    /**
     * @param $discountExpiresAt
     */
    protected ?int $discount;
    protected ?int $discountExpiresAt;
    /**
     * @param $price
     * @param $discount
     * @param $discountExpiresAt
     */
    protected ?int $discountDifference;

    /**
     * @param $price
     * @param $discount
     * @param $discountExpiresAt
     */
    protected ?int $discountPrice;

    /**
     * @lazyLoad
     */
    protected array $authors;

    /**
     * @lazyLoad
     */
    protected array $themes;

    private TermRepository $termRepo;

    public function __construct()
    {
        parent::__construct();

        // Repositories
        $this->termRepo = new TermRepository();

        $this->setColumnsMap([
            'poems_price'               => 'price',
            'poems_discount'            => 'discount',
            'poems_discount_expires_at' => 'discountExpiresAt',
        ]);
    }

    public function getPrice()
    {
        if (isset($this->price)) {
            return $this->price;
        }

        return 'Стоимость не указана';
    }

    public function getDiscountPrice()
    {
        if ($this->discountExpiresAt && $this->discountExpiresAt <= time()) {
            return 'Стоимость не указана';
        }

        if (isset($this->discountPrice)) {
            return $this->discountPrice;
        }

        if (empty($this->price) || empty($this->discount)) {
            return 'Стоимость не указана';
        }

        return $this->discountPrice = $this->price - ($this->price / 100 * $this->discount);
    }

    public function getDiscountDifference()
    {
        if ($this->discountExpiresAt && $this->discountExpiresAt <= time()) {
            return 'Стоимость не указана';
        }

        if (isset($this->discountDifference)) {
            return $this->discountDifference;
        }

        if (empty($this->price) || empty($this->discount)) {
            return 'Стоимость не указана';
        }

        return $this->discountDifference = $this->price / 100 * $this->discount;
    }

    public function getDiscount(): ?int
    {
        if (isset($this->discountExpiresAt) && $this->discountExpiresAt <= time()) {
            return null;
        }

        return $this->discount;
    }

    public function getDiscountExpiresAt(): ?string
    {
        if (!isset($this->discountExpiresAt) || $this->discountExpiresAt <= time()) {
            return null;
        }

        return 'до ' . \DateTime::createFromFormat('U', $this->discountExpiresAt)->format('d.m');
    }

    public function getAuthors(): array
    {
        if (!empty($this->authors)) {
            return $this->authors;
        }

        $gg = $this->termRepo->setTaxonomy('poems_authors')->findAll(['fields' => ['title', 'uri']]);

        return $this->authors = $gg;
    }

    public function getThemes(): array
    {
        if (!empty($this->themes)) {
            return $this->themes;
        }

        $gg = $this->termRepo->setTaxonomy('poems_themes')->findAll(['fields' => ['title', 'uri']]);

        return $this->themes = $gg;
    }
}
