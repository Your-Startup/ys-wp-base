<?php
namespace YS\Core\Service;

use YS\Core\Util\StringUtil;

class ContentFormatter extends AbstractService
{
    /**
     * Подготавливает контент к выдаче. Обрабатывает все шорткоды и добавляет параграфы в тексте.
     *
     * @param string|null $content Содержимое
     *
     * @return string
     */
    public static function prepareContent(?string $content)
    {
        if (!$content) {
            return '';
        }

        static $allowedHtml;

        if (!isset($allowedHtml)) {
            $allowedHtml = wp_kses_allowed_html('post');

            $allowedHtml['iframe'] = [
                'src'                   => true,
                'width'                 => true,
                'height'                => true,
                'class'                 => true,
                'scrolling'             => true,
                'style'                 => true,
                'frameborder'           => true,
                'webkitAllowFullScreen' => true,
                'mozallowfullscreen'    => true,
                'allowFullScreen'       => true
            ];

            unset($allowedHtml['img']['style']);
        }

        // TODO: 06.11.20 vadeemch81 / Временно удаление [game ...] шорткода
        $content = preg_replace('~\[/?game[^\]]*\]~', '', $content);

        $content = wp_kses($content, $allowedHtml);
        $content = StringUtil::autoP($content);
        $content = shortcode_unautop($content);
        $content = str_replace("\n", ' ', $content);

        // $content = do_shortcode($content);

        return $content;
    }
}