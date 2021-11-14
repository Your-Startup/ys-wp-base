<?php
namespace RB\Site\Service;

class SiteService extends AbstractService
{
    /**
     * Подготавливает массив с пунктами меню
     *
     * @param array $items
     *
     * @return array Ассоциативный массив пунктов меню
     */
    public function prepareMenuItems(array $items)
    {
        $prepared = [];
        $siteUri  = site_url();

        foreach ($items as $entry) {
            $prepared[] = [
                'title'     => $entry->title,
                'uri'       => str_replace($siteUri, '', $entry->url),
                'submenu'   => !empty($entry->subs) ? $this->prepareMenuItems($entry->subs) : []
            ];
        }

        return $prepared;
    }

}