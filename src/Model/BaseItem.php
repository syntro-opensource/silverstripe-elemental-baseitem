<?php

namespace Syntro\SilverStripeElementalBaseitem\Model;

use BucklesHusky\FontAwesomeIconPicker\Forms\FAPickerField;
use DNADesign\Elemental\Forms\TextCheckboxGroupField;
use SilverStripe\Assets\Image;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Director;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;
use SilverStripe\Versioned\Versioned;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use Syntro\SilverStripeElementalBaseitems\Elements\BootstrapSectionBaseElement;
use gorriecoe\LinkField\LinkField;
use gorriecoe\Link\Models\Link;

/**
 * Base Item handling permissions related to the elements and streamlining
 * templating
 *
 * @author Matthias Leutenegger <hello@syntro.ch>
 */
class BaseItem extends DataObject
{
    /**
     * @config
     * @var string
     */
    private static $singular_name = 'Item';

    /**
     * @config
     * @var string
     */
    private static $plural_name = 'Items';

    /**
     * Display a show title button
     *
     * @config
     * @var boolean
     */
    private static $displays_title_in_template = true;

    /**
     * @config
     * @var array
     */
    private static $db = [
        'Title' => 'Varchar',
        'ShowTitle' => 'Varchar',
        'Sort' => 'Int',
    ];

    /**
     * @config
     * @var string
     */
    private static $default_sort = 'Sort';

    /**
     * @config
     * @var array
     */
    private static $searchable_fields = array(
        'Title'
    );

    /**
     * @config
     * @var array
     */
    private static $extensions = [
        Versioned::class,
    ];

    /**
     * Adds Publish button.
     * @config
     * @var bool
     */
    private static $versioned_gridfield_extensions = true;

    /**
     * @config
     * @var string
     */
    private static $table_name = 'ElementalBaseItem';

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {
            /** @var FieldList $fields */
            $fields->removeByName([
                'Sort',
            ]);

            // Add a combined Title/ShowTitle field to achieve elemental look
            $fields->removeByName('ShowTitle');
            if ($this->config()->get('displays_title_in_template')) {
                $fields->replaceField(
                    'Title',
                    TextCheckboxGroupField::create()
                        ->setName($this->fieldLabel('Title'))
                );
            }
        });
        return parent::getCMSFields();
    }


    /**
     * @return SiteTree|null
     */
    public function getPage()
    {
        $page = Director::get_current_page();
        // because $page can be a SiteTree or Controller
        return $page instanceof SiteTree ? $page : null;
    }

    /**
     * Basic permissions, defaults to page perms where possible.
     *
     * @param \SilverStripe\Security\Member|null $member current member
     * @return boolean
     */
    public function canView($member = null)
    {
        $extended = $this->extendedCan(__FUNCTION__, $member);
        if ($extended !== null) {
            return $extended;
        }

        if ($page = $this->getPage()) {
            return $page->canView($member);
        }

        return Permission::check('CMS_ACCESS', 'any', $member);
    }

    /**
     * Basic permissions, defaults to page perms where possible.
     *
     * @param \SilverStripe\Security\Member|null $member current member
     *
     * @return boolean
     */
    public function canEdit($member = null)
    {
        $extended = $this->extendedCan(__FUNCTION__, $member);
        if ($extended !== null) {
            return $extended;
        }

        if ($page = $this->getPage()) {
            return $page->canEdit($member);
        }

        return Permission::check('CMS_ACCESS', 'any', $member);
    }

    /**
     * Basic permissions, defaults to page perms where possible.
     *
     * Uses archive not delete so that current stage is respected i.e if a
     * element is not published, then it can be deleted by someone who doesn't
     * have publishing permissions.
     *
     * @param \SilverStripe\Security\Member|null $member current member
     *
     * @return boolean
     */
    public function canDelete($member = null)
    {
        $extended = $this->extendedCan(__FUNCTION__, $member);
        if ($extended !== null) {
            return $extended;
        }

        if ($page = $this->getPage()) {
            return $page->canArchive($member);
        }

        return Permission::check('CMS_ACCESS', 'any', $member);
    }

    /**
     * Basic permissions, defaults to page perms where possible.
     *
     * @param \SilverStripe\Security\Member|null $member  current member
     * @param array                              $context additional context
     *
     * @return boolean
     */
    public function canCreate($member = null, $context = array())
    {
        $extended = $this->extendedCan(__FUNCTION__, $member);
        if ($extended !== null) {
            return $extended;
        }

        return Permission::check('CMS_ACCESS', 'any', $member);
    }
    //
    // /**
    //  * forTemplate - render this Object with a Template
    //  *
    //  * @return string|null
    //  */
    // public function forTemplate()
    // {
    //     $templates = $this->getRenderTemplates();
    //
    //     if ($templates) {
    //         return $this->renderWith($templates);
    //     }
    //
    //     return null;
    // }
    //
    // /**
    //  * getRenderTemplates - retrieve the templates for this element using
    //  * the linked section
    //  *
    //  * @return array
    //  */
    // public function getRenderTemplates()
    // {
    //     $templates = [];
    //     $ItemName = explode('\\', static::class);
    //     $ItemName = array_pop($ItemName);
    //     $relations = static::config()->get('has_one');
    //     foreach ($relations as $key => $relation) {
    //         if (is_subclass_of($relation, BootstrapSectionBaseElement::class)) {
    //             $section = self::relObject($key);
    //             $templateName = $ItemName;
    //             if ($style = $section->Style) {
    //                 $templates[] = $section->getSubTemplate($templateName."_$style");
    //             }
    //             $templates[] = $section->getSubTemplate($templateName);
    //         }
    //     }
    //     return $templates;
    // }

    /**
     * Event handler called before writing to the database.
     *
     * @return void
     */
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        if (!$this->Sort) {
            $this->Sort = static::get()->max('Sort') + 1;
        }
    }
}
