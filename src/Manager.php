<?php

namespace NightFury\Option;

use Illuminate\Support\Collection;
use NF\Facades\Request;
use NightFury\Option\Abstracts\Input;
use NightFury\Option\Abstracts\Page;
use NightFury\Option\Inputs\Email;
use NightFury\Option\Inputs\Image;
use NightFury\Option\Inputs\Select;
use NightFury\Option\Inputs\Text;

class Manager
{
    const MENU_SLUG           = 'nf-theme-option';
    const NTO_SAVED_SUCCESSED = 'nto_saved_successed';

    public $pages;

    public function add($data)
    {
        $page = new Page();
        $page->setName($data['name']);
        $fields = new Collection();
        foreach ($data['fields'] as $field) {
            switch ($field['type']) {
                case Input::TEXT:
                    $input              = new Text();
                    $input->label       = isset($field['label']) ? $field['label'] : $input->label;
                    $input->name        = isset($field['name']) ? $field['name'] : $input->name;
                    $input->description = isset($field['description']) ? $field['description'] : $input->description;
                    $input->required    = isset($field['required']) ? $field['required'] : $input->required;
                    break;
                case Input::EMAIL:
                    $input              = new Email();
                    $input->label       = isset($field['label']) ? $field['label'] : $input->label;
                    $input->name        = isset($field['name']) ? $field['name'] : $input->name;
                    $input->description = isset($field['description']) ? $field['description'] : $input->description;
                    $input->required    = isset($field['required']) ? $field['required'] : $input->required;
                    break;
                case Input::SELECT:
                    $input              = new Select();
                    $input->label       = isset($field['label']) ? $field['label'] : $input->label;
                    $input->name        = isset($field['name']) ? $field['name'] : $input->name;
                    $input->description = isset($field['description']) ? $field['description'] : $input->description;
                    $input->options     = isset($field['options']) ? $field['options'] : $input->options;
                    $input->required    = isset($field['required']) ? $field['required'] : $input->required;
                    break;
                case Input::IMAGE:
                    $input              = new Image();
                    $input->label       = isset($field['label']) ? $field['label'] : $input->label;
                    $input->name        = isset($field['name']) ? $field['name'] : $input->name;
                    $input->description = isset($field['description']) ? $field['description'] : $input->description;
                    $input->options     = isset($field['options']) ? $field['options'] : $input->options;
                    $input->required    = isset($field['required']) ? $field['required'] : $input->required;
                    break;

                default:
                    # code...
                    break;
            }
            $fields->push($input);
        }
        $page->setFields($fields);
        if ($this->pages == null) {
            $this->pages = new Collection([$page]);
        } else {
            $this->pages->push($page);
        }
    }

    public function getTabUrl($name)
    {
        return get_admin_url() . 'admin.php?page=' . self::MENU_SLUG . '&tab=' . str_slug($name);
    }

    /**
     * Retrievie the list of pages
     *
     * @return \Illuminate\Support\Collection
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * Get page by slug
     *
     * @return array
     */

    public function getPage($slug)
    {
        if (!isset($slug)) {
            if ($this->getPages()->count() == 0) {
                throw new \Exception("Page not found", 1);
            }
            return $this->getPages()->first();
        }
        $k = $this->getPages()->search(function ($item) use ($slug) {
            return str_slug($item->name) == str_slug($slug);
        });
        if ($k === false) {
            throw new \Exception("Page not found", 1);
        }
        return $this->getPages()->get($k);
    }

    /**
     * Determine page by slug
     *
     * @return boolean
     */

    public function isPage($name)
    {
        if (Request::has('tab')) {
            return Request::get('tab') === str_slug($name);
        } else {
            return str_slug($this->getPages()->first()->name) === str_slug($name);
        }
    }

}