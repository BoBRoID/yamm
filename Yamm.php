<?php
namespace bobroid\yamm;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * @author Nikolai Gilko
 * @since 1.0
 */
class Yamm extends Widget
{
    /**
     * @var array|string логотип, отображаемый в левой части меню
     * если array:
     *          content - логотип (ссылка на изображение), или текст
     *          link    - ссылка на логотипе (по умолчанию '#')
     */
    public $logo = [];

    public $items = [];

    public $options = [];
    /**
     * @var array the dropdown widget options
     */
    public $dropdownOptions = [];
    
    /**
     * @var string the caret indicator to display for dropdowns
     */
    public $dropdownIndicator = ' <span class="caret"></span>';



    protected $defaultMenuLabel = 'Меню';
    protected $defaultSearchLabel = 'Поиск';
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->options['headerOptions'] = isset($this->options['headerOptions']) ? $this->options['headerOptions'] : [];

        $this->options['headerOptions']['class'] = isset($this->options['headerOptions']['class']) ? 'cd-main-header ' . $this->options['headerOptions']['class'] : 'cd-main-header';

        $this->options['menuLabel'] = isset($this->options['menuLabel']) ? $this->options['menuLabel'] : $this->defaultMenuLabel;
        $this->options['searchLabel'] = isset($this->options['searchLabel']) ? $this->options['searchLabel'] : $this->defaultSearchLabel;
        YammAsset::register($this->getView());
    }
    
    /**
     * @inheritdoc
     */
    protected function isChildActive($items, &$active)
    {

    }

    protected function renderLogo(){
        $content = $link = '';

        if(is_array($this->logo) && !empty($this->logo)){
            $content = $this->logo['content'];
            $link = $this->logo['link'];
        }else{
            if(empty($this->logo)){
                return;
            }

            $content = $this->logo;
        }

        $link = empty($link) ? '#' : $link;

        if(preg_match('/[\w\-]+\.(jpg|png|gif|jpeg)/', $content)){
            $content = '<img src="'.$content.'">';
        }

        return '<a class="cd-logo" href="'.$link.'">'.$content.'</a>';
    }

    /**
     * @inheritdoc
     */
    public function renderItem($item, $parent = [])
    {
        $dropdown = false;
        $options = [];

        if(isset($item['items']) && !empty($item['items'])){
            $dropdown = true;
            $options = [
                'class' =>  'has-children'
            ];
        }

        $r = Html::a(!isset($item['label']) ? $item : $item['label'], isset($item['url']) ? $item['url'] : '#');

        if($dropdown){
            $subitems = '';

            if(!isset($item['options'])){
                $item['options']['class'] = 'is-hidden';
            }else{
                if(isset($item['options']['type'])){
                    $item['options']['class'] .= isset($item['options']['class']) ? $item['options']['class'].' is-hidden' : 'cd-secondary-nav is-hidden';
                }
            }

            $subitems .= Html::tag('li', Html::tag('a', isset($parent['label']) ? $parent['label'] : '', [
                'href'  =>  '#0'
            ]), [
                'class' =>  'go-back'
            ]);

            foreach($item['items'] as $sItem){
                $subitems .= $this->renderItem($sItem, $item);
            }

            $r .= Html::tag('ul', $subitems, $item['options']);
        }

        $r = Html::tag('li', $r, $options);

        return $r;
    }

    protected function renderMenu(){
        $header = Html::tag('header', $this->renderLogo().'
    <ul class="cd-header-buttons">
        <li><a class="cd-search-trigger" href="#cd-search">'.$this->options['searchLabel'].'<span></span></a></li>
        <li><a class="cd-nav-trigger" href="#cd-primary-nav">'.$this->options['menuLabel'].'<span></span></a></li>
    </ul>', $this->options['headerOptions']);

        $header .= '<div class="cd-overlay"></div>';
        $header .= '<nav class="cd-nav">
    <ul id="cd-primary-nav" class="cd-primary-nav is-fixed">';

        foreach($this->items as $item){
            $header .= $this->renderItem($item);
        }

        /*$header .= '<li class="has-children">
            <a href="http://codyhouse.co/?p=409">Clothing</a>

            <ul class="cd-secondary-nav is-hidden">
                <li class="go-back"><a href="#0">Menu</a></li>
                <li class="see-all"><a href="http://codyhouse.co/?p=409">All Clothing</a></li>
                <li class="has-children">
                    <a href="http://codyhouse.co/?p=409">Accessories</a>

                    <ul class="is-hidden">
                        <li class="go-back"><a href="#0">Clothing</a></li>
                        <li class="see-all"><a href="http://codyhouse.co/?p=409">All Accessories</a></li>
                        <li class="has-children">
                            <a href="#0">Beanies</a>

                            <ul class="is-hidden">
                                <li class="go-back"><a href="#0">Accessories</a></li>
                                <li class="see-all"><a href="http://codyhouse.co/?p=409">All Benies</a></li>
                                <li><a href="http://codyhouse.co/?p=409">Caps &amp; Hats</a></li>
                                <li><a href="http://codyhouse.co/?p=409">Gifts</a></li>
                                <li><a href="http://codyhouse.co/?p=409">Scarves &amp; Snoods</a></li>
                            </ul>
                        </li>
                        <li class="has-children">
                            <a href="#0">Caps &amp; Hats</a>

                            <ul class="is-hidden">
                                <li class="go-back"><a href="#0">Accessories</a></li>
                                <li class="see-all"><a href="http://codyhouse.co/?p=409">All Caps &amp; Hats</a></li>
                                <li><a href="http://codyhouse.co/?p=409">Beanies</a></li>
                                <li><a href="http://codyhouse.co/?p=409">Caps</a></li>
                                <li><a href="http://codyhouse.co/?p=409">Hats</a></li>
                            </ul>
                        </li>
                        <li><a href="http://codyhouse.co/?p=409">Glasses</a></li>
                        <li><a href="http://codyhouse.co/?p=409">Gloves</a></li>
                        <li><a href="http://codyhouse.co/?p=409">Jewellery</a></li>
                        <li><a href="http://codyhouse.co/?p=409">Scarves</a></li>
                        <li><a href="http://codyhouse.co/?p=409">Wallets</a></li>
                        <li><a href="http://codyhouse.co/?p=409">Watches</a></li>
                    </ul>
                </li>

                <li class="has-children">
                    <a href="http://codyhouse.co/?p=409">Bottoms</a>

                    <ul class="is-hidden">
                        <li class="go-back"><a href="#0">Clothing</a></li>
                        <li class="see-all"><a href="http://codyhouse.co/?p=409">All Bottoms</a></li>
                        <li><a href="http://codyhouse.co/?p=409">Casual Trousers</a></li>
                        <li class="has-children">
                            <a href="#0">Jeans</a>

                            <ul class="is-hidden">
                                <li class="go-back"><a href="#0">Bottoms</a></li>
                                <li class="see-all"><a href="http://codyhouse.co/?p=409">All Jeans</a></li>
                                <li><a href="http://codyhouse.co/?p=409">Ripped</a></li>
                                <li><a href="http://codyhouse.co/?p=409">Skinny</a></li>
                                <li><a href="http://codyhouse.co/?p=409">Slim</a></li>
                                <li><a href="http://codyhouse.co/?p=409">Straight</a></li>
                            </ul>
                        </li>
                        <li><a href="#0">Leggings</a></li>
                        <li><a href="#0">Shorts</a></li>
                    </ul>
                </li>

                <li class="has-children">
                    <a href="http://codyhouse.co/?p=409">Jackets</a>

                    <ul class="is-hidden">
                        <li class="go-back"><a href="#0">Clothing</a></li>
                        <li class="see-all"><a href="http://codyhouse.co/?p=409">All Jackets</a></li>
                        <li><a href="http://codyhouse.co/?p=409">Blazers</a></li>
                        <li><a href="http://codyhouse.co/?p=409">Bomber jackets</a></li>
                        <li><a href="http://codyhouse.co/?p=409">Denim Jackets</a></li>
                        <li><a href="http://codyhouse.co/?p=409">Duffle Coats</a></li>
                        <li><a href="http://codyhouse.co/?p=409">Leather Jackets</a></li>
                        <li><a href="http://codyhouse.co/?p=409">Parkas</a></li>
                    </ul>
                </li>

                <li class="has-children">
                    <a href="http://codyhouse.co/?p=409">Tops</a>

                    <ul class="is-hidden">
                        <li class="go-back"><a href="#0">Clothing</a></li>
                        <li class="see-all"><a href="http://codyhouse.co/?p=409">All Tops</a></li>
                        <li><a href="http://codyhouse.co/?p=409">Cardigans</a></li>
                        <li><a href="http://codyhouse.co/?p=409">Coats</a></li>
                        <li><a href="http://codyhouse.co/?p=409">Hoodies &amp; Sweatshirts</a></li>
                        <li><a href="http://codyhouse.co/?p=409">Jumpers</a></li>
                        <li><a href="http://codyhouse.co/?p=409">Polo Shirts</a></li>
                        <li><a href="http://codyhouse.co/?p=409">Shirts</a></li>
                        <li class="has-children">
                            <a href="#0">T-Shirts</a>

                            <ul class="is-hidden">
                                <li class="go-back"><a href="#0">Tops</a></li>
                                <li class="see-all"><a href="http://codyhouse.co/?p=409">All T-shirts</a></li>
                                <li><a href="http://codyhouse.co/?p=409">Plain</a></li>
                                <li><a href="http://codyhouse.co/?p=409">Print</a></li>
                                <li><a href="http://codyhouse.co/?p=409">Striped</a></li>
                                <li><a href="http://codyhouse.co/?p=409">Long sleeved</a></li>
                            </ul>
                        </li>
                        <li><a href="http://codyhouse.co/?p=409">Vests</a></li>
                    </ul>
                </li>
            </ul>
        </li>

        <li class="has-children">
            <a href="http://codyhouse.co/?p=409">Gallery</a>

            <ul class="cd-nav-gallery is-hidden">
                <li class="go-back"><a href="#0">Menu</a></li>
                <li class="see-all"><a href="http://codyhouse.co/?p=409">Browse Gallery</a></li>
                <li>
                    <a class="cd-nav-item" href="http://codyhouse.co/?p=409">
                        <img src="img/img.jpg" alt="Product Image">
                        <h3>Product #1</h3>
                    </a>
                </li>

                <li>
                    <a class="cd-nav-item" href="http://codyhouse.co/?p=409">
                        <img src="img/img.jpg" alt="Product Image">
                        <h3>Product #2</h3>
                    </a>
                </li>

                <li>
                    <a class="cd-nav-item" href="http://codyhouse.co/?p=409">
                        <img src="img/img.jpg" alt="Product Image">
                        <h3>Product #3</h3>
                    </a>
                </li>

                <li>
                    <a class="cd-nav-item" href="http://codyhouse.co/?p=409">
                        <img src="img/img.jpg" alt="Product Image">
                        <h3>Product #4</h3>
                    </a>
                </li>
            </ul>
        </li>

        <li class="has-children">
            <a href="http://codyhouse.co/?p=409">Services</a>
            <ul class="cd-nav-icons is-hidden">
                <li class="go-back"><a href="#0">Menu</a></li>
                <li class="see-all"><a href="http://codyhouse.co/?p=409">Browse Services</a></li>
                <li>
                    <a class="cd-nav-item item-1" href="http://codyhouse.co/?p=409">
                        <h3>Service #1</h3>
                        <p>This is the item description</p>
                    </a>
                </li>

                <li>
                    <a class="cd-nav-item item-2" href="http://codyhouse.co/?p=409">
                        <h3>Service #2</h3>
                        <p>This is the item description</p>
                    </a>
                </li>

                <li>
                    <a class="cd-nav-item item-3" href="http://codyhouse.co/?p=409">
                        <h3>Service #3</h3>
                        <p>This is the item description</p>
                    </a>
                </li>

                <li>
                    <a class="cd-nav-item item-4" href="http://codyhouse.co/?p=409">
                        <h3>Service #4</h3>
                        <p>This is the item description</p>
                    </a>
                </li>

                <li>
                    <a class="cd-nav-item item-5" href="http://codyhouse.co/?p=409">
                        <h3>Service #5</h3>
                        <p>This is the item description</p>
                    </a>
                </li>

                <li>
                    <a class="cd-nav-item item-6" href="http://codyhouse.co/?p=409">
                        <h3>Service #6</h3>
                        <p>This is the item description</p>
                    </a>
                </li>

                <li>
                    <a class="cd-nav-item item-7" href="http://codyhouse.co/?p=409">
                        <h3>Service #7</h3>
                        <p>This is the item description</p>
                    </a>
                </li>

                <li>
                    <a class="cd-nav-item item-8" href="http://codyhouse.co/?p=409">
                        <h3>Service #8</h3>
                        <p>This is the item description</p>
                    </a>
                </li>
            </ul>
        </li>

        <li class="has-children">
            <a href="http://codyhouse.co/?p=409">Services</a>
            <ul class="cd-nav-icons is-hidden">
                <li class="go-back"><a href="#0">Menu</a></li>
                <li class="see-all"><a href="http://codyhouse.co/?p=409">Browse Services</a></li>
                <li>
                    <a class="cd-nav-item item-1" href="http://codyhouse.co/?p=409">
                        <h3>Service #1</h3>
                        <p>This is the item description</p>
                    </a>
                </li>

                <li>
                    <a class="cd-nav-item item-2" href="http://codyhouse.co/?p=409">
                        <h3>Service #2</h3>
                        <p>This is the item description</p>
                    </a>
                </li>

                <li>
                    <a class="cd-nav-item item-3" href="http://codyhouse.co/?p=409">
                        <h3>Service #3</h3>
                        <p>This is the item description</p>
                    </a>
                </li>

                <li>
                    <a class="cd-nav-item item-4" href="http://codyhouse.co/?p=409">
                        <h3>Service #4</h3>
                        <p>This is the item description</p>
                    </a>
                </li>

                <li>
                    <a class="cd-nav-item item-5" href="http://codyhouse.co/?p=409">
                        <h3>Service #5</h3>
                        <p>This is the item description</p>
                    </a>
                </li>

                <li>
                    <a class="cd-nav-item item-6" href="http://codyhouse.co/?p=409">
                        <h3>Service #6</h3>
                        <p>This is the item description</p>
                    </a>
                </li>

                <li>
                    <a class="cd-nav-item item-7" href="http://codyhouse.co/?p=409">
                        <h3>Service #7</h3>
                        <p>This is the item description</p>
                    </a>
                </li>

                <li>
                    <a class="cd-nav-item item-8" href="http://codyhouse.co/?p=409">
                        <h3>Service #8</h3>
                        <p>This is the item description</p>
                    </a>
                </li>
            </ul>
        </li>

        <li><a href="http://codyhouse.co/?p=409">Standard</a></li>';
*/
$header .= '</ul>
</nav>
<div id="cd-search" class="cd-search">
    <form>
        <input type="search" placeholder="Search...">
    </form>
</div>';

        return $header;
    }

    public function run(){
        return self::renderMenu();
    }
}
