<?php

class SuiteMozaik {

    private $mozaikPath = 'include/javascript/mozaik';
    private $vendorPath;

    private static $defaultThumbnails = array(
        'headline' => array(
            'label' => 'Headline',
            //'tpl' => 'tpls/default/headline.html',
            'tpl' => 'string:<p><h1>Add your headline here..</h1></p>',
            'thumbnail' => 'tpls/default/thumbs/headline.png',
        ),
        'content' => array(
            'label' => 'Content',
            'tpl' => 'string:<p>{lipsum}</p>',
            'thumbnail' => 'tpls/default/thumbs/content1.png',
        ),
        'content2' => array(
            'label' => 'Content with two columns',
            'tpl' => 'string:<table style="width:100%;"><tbody><tr><td>{lipsum}</td><td>{lipsum}</td></tr></tbody></table>',
            'thumbnail' => 'tpls/default/thumbs/content2.jpg',
        ),
        'content3' => array(
            'label' => 'Content with three columns',
            'tpl' => 'string:<table style="width:100%;"><tbody><tr><td>{lipsum}</td><td>{lipsum}</td><td>{lipsum}</td></tr></tbody></table>',
            'thumbnail' => 'tpls/default/thumbs/content3.jpg',
        ),
        'image1left' => array(
            'label' => 'Content with left image',
            'tpl' => 'string:<table style="width:100%;"><tbody><tr><td>{imageSmall}</td><td>{lipsum}</td></tr></tbody></table>',
            'thumbnail' => 'tpls/default/thumbs/image1left.jpg',
        ),
        'image1right' => array(
            'label' => 'Content with right image',
            'tpl' => 'string:<table style="width:100%;"><tbody><tr><td>{lipsum}</td><td>{imageSmall}</td></tr></tbody></table>',
            'thumbnail' => 'tpls/default/thumbs/image1right.jpg',
        ),
        'image2' => array(
            'label' => 'Content with two image',
            'tpl' => 'string:<table style="width:100%;"><tbody><tr><td>{imageSmall}</td><td>{lipsum}</td><td>{imageSmall}</td><td>{lipsum}</td></tr></tbody></table>',
            'thumbnail' => 'tpls/default/thumbs/image2.jpg',
        ),
        'image3' => array(
            'label' => 'Content with three image',
            'tpl' => 'string:<table style="width:100%;"><tbody><tr><td>{image}</td><td>{image}</td><td>{image}</td></tr><tr><td>{lipsum}</td><td>{lipsum}</td><td>{lipsum}</td></tr></tbody></table>',
            'thumbnail' => 'tpls/default/thumbs/image3.jpg',
        ),
        'footer' => array(
            'label' => 'Footer',
            //'tpl' => 'tpls/default/footer.html',
            'tpl' => 'string:<p class="footer">Take your footer contents and information here..</p>',
            'thumbnail' => 'tpls/default/thumbs/footer.png',
        ),
    );

    private $thumbsCache = array();

    private $autoInsertThumbnails = true;

    private static $devMode = false;

    public function __construct() {
        $this->vendorPath = $this->mozaikPath . '/vendor';
        if($this->autoInsertThumbnails) {
            if(count($this->getThumbs())==0 || self::$devMode) {
                $ord = 0;
                foreach(self::$defaultThumbnails as $thumbName => $thumbData) {
                    $templateSectionLine = new TemplateSectionLine();
                    $templateSectionLine->name = $thumbData['label'];
                    $templateSectionLine->description = preg_replace('/^string:/', '', $thumbData['tpl']);
                    $templateSectionLine->description = str_replace('{lipsum}', $this->getContentLipsum(), $templateSectionLine->description);
                    $templateSectionLine->description = str_replace('{imageSmall}', $this->getContentImageSample(130), $templateSectionLine->description);
                    $templateSectionLine->description = str_replace('{image}', $this->getContentImageSample(), $templateSectionLine->description);
                    $templateSectionLine->thumbnail = file_exists($this->mozaikPath . '/' . $thumbData['thumbnail']) ? $this->mozaikPath . '/' . $thumbData['thumbnail'] : null;
                    $templateSectionLine->ord = ++$ord;
                    $templateSectionLine->save();
                }
            }
            $this->thumbsCache = array();
        }
    }

    private function getContentLipsum() {
        return 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam tempus odio ante, in feugiat ex pretium eu. In pharetra tincidunt urna et malesuada. Etiam aliquet auctor justo eu placerat. In nec sollicitudin enim. Nulla facilisi. In viverra velit turpis, et lobortis nunc eleifend id. Curabitur semper tincidunt vulputate. Nullam fermentum pellentesque ullamcorper.';
    }

    private function getContentImageSample($width = null) {
        if(is_numeric($width)) {
            $width = ' width="' . $width . '"';
        }
        else {
            $width = '';
        }
        $splits = explode('index.php', $_SERVER['REQUEST_URI']);
        $url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . $splits[0];
        $image = '<img src="' . $url . $this->mozaikPath . '/tpls/default/images/sample.jpg" ' . $width . ' />';
        return $image;
    }

    public function getDependenciesHTML() {
        $html = <<<HTML
<script src='{$this->vendorPath}/tinymce/tinymce/tinymce.min.js'></script>
<script src="{$this->vendorPath}/gymadarasz/imagesloaded/imagesloaded.pkgd.min.js"></script>

<!-- for color picker plugin -->
<link rel="stylesheet" media="screen" type="text/css" href="{$this->vendorPath}/../colorpicker/css/colorpicker.css" />
<script type="text/javascript" src="{$this->vendorPath}/../colorpicker/js/colorpicker.js"></script>
HTML;
        return $html;
    }

    public function getIncludeHTML() {
        $html = <<<HTML
<link rel="stylesheet" href="{$this->mozaikPath}/jquery.mozaik.css">
<script src='{$this->mozaikPath}/jquery.mozaik.js'></script>
HTML;
        return $html;
    }

    public function getElementHTML($contents = '', $textareaId = null, $elementId = 'mozaik', $width = 'initial', $thumbs = array()) {
        if(is_numeric($width)) {
            $width .= 'px';
        }
        if(!$thumbs) {
            $thumbs = self::$defaultThumbnails;
        }
        $thumbsJSON = json_encode($thumbs);
        $refreshTextareaScript = '';
        if($textareaId) {
            $refreshTextareaScript = $this->getRefreshTextareaScript($textareaId, $elementId, $width);
        }
        $html = <<<HTML
<div id="{$elementId}">{$contents}</div>
<script type="text/javascript">
    $(function() {
        // initialize

        if(typeof window.mozaikSettings == 'undefined') {
            window.mozaikSettings = {};
        }

        window.mozaikSettings.{$elementId} = {
            base: '{$this->mozaikPath}/',
            thumbs: {$thumbsJSON},
            editables: 'editable',
            style: 'tpls/default/styles/default.css',
            namespace: false,
            ace: false,
            width: '{$width}'
        };

        window.plgBackground.image = '{$this->mozaikPath}/' + window.plgBackground.image;

        $('#{$elementId}').mozaik(window.mozaikSettings.{$elementId});
    });
    // refresh textarea
    {$refreshTextareaScript}
</script>
HTML;
        return $html;
    }

    public function getAllHTML($contents = '', $textareaId = null, $elementId = 'mozaik', $width = 'initial', $group = '') {
        if(is_numeric($width)) {
            $width .= 'px';
        }
        $mozaikHTML = $this->getDependenciesHTML();
        $mozaikHTML .= $this->getIncludeHTML();
        $thumbs = $this->getThumbs($group);
        $mozaikHTML .= $this->getElementHTML($contents, $textareaId, $elementId, $width, $thumbs);
        return $mozaikHTML;
    }

    private function getRefreshTextareaScript($textareaId, $elementId, $width = 'initial') {
        if(is_numeric($width)) {
            $width .= 'px';
        }
        $js = <<<SCRIPT
$(window).mouseup(function(){
     $('#{$textareaId}').val($('#{$elementId}').getMozaikValue({width: '{$width}'}));
});
SCRIPT;
        return $js;
    }

    private function getThumbs($group = '') {
        $cacheGroup = 'cached_' . $group;

        if(!isset($this->thumbsCache[$cacheGroup])) {
            $db = DBManagerFactory::getInstance();
            $_group = $db->quote($group);
            $templateSectionLineBean = BeanFactory::getBean('TemplateSectionLine');
            $thumbBeans = $templateSectionLineBean->get_full_list('ord', "(grp LIKE '$_group' OR grp IS NULL)");
            $thumbs = array();
            if ($thumbBeans) {
                foreach ($thumbBeans as $thumbBean) {
                    $thumbs[$thumbBean->name] = array(
                        'label' => $thumbBean->thumbnail ? $this->getThumbImageHTML($thumbBean->thumbnail, $thumbBean->name) : $thumbBean->name,
                        'tpl' => 'string:' . html_entity_decode($thumbBean->description),
                    );
                }
            }
            $this->thumbsCache[$cacheGroup] = $thumbs;
        }

        $thumbs = $this->thumbsCache[$cacheGroup];

        return $thumbs;
    }

    private function getThumbImageHTML($src, $label) {
        if(file_exists($src)) {
            $html = '<img src="' . $src. '" alt="' . $label . '">';
        }
        else {
            $html = $label;
        }
        return $html;
    }

}