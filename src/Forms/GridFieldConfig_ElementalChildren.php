<?php

namespace Syntro\SilverStripeElementalBaseitem\Forms;

use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldButtonRow;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use SilverStripe\Forms\GridField\GridFieldFilterHeader;
use SilverStripe\Forms\GridField\GridFieldPageCount;
use SilverStripe\Forms\GridField\GridFieldPaginator;
use SilverStripe\Forms\GridField\GridFieldSortableHeader;
use SilverStripe\Forms\GridField\GridFieldToolbarHeader;
use SilverStripe\Forms\GridField\GridField_ActionMenu;
use SilverStripe\Versioned\GridFieldArchiveAction;
use SilverStripe\Versioned\VersionedGridFieldState\VersionedGridFieldState;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use Symbiote\GridFieldExtensions\GridFieldTitleHeader;

/**
 * Gridfield config specifically crafted for elemental blocks
 * which have children objects
 *
 * @author Matthias Leutenegger <hello@syntro.ch>
 */
class GridFieldConfig_ElementalChildren extends GridFieldConfig
{
    /**
     * __construct
     *
     * @param  int     $itemsPerPage   = null   how many items per page
     * @param  boolean $showPagination = null wether to show pagination in the detail form
     * @param  boolean $showAdd        = null        wether to show the add button in the detail form
     * @return void
     */
    public function __construct($itemsPerPage = null, $showPagination = null, $showAdd = null)
    {
        parent::__construct();

        $this->addComponent(GridFieldButtonRow::create('before'));
        $this->addComponent(GridFieldAddNewButton::create('buttons-before-left'));
        $this->addComponent(GridFieldToolbarHeader::create());
        $this->addComponent($filter = GridFieldFilterHeader::create());
        $this->addComponent(GridFieldDataColumns::create());
        $this->addComponent(new VersionedGridFieldState());
        $this->addComponent(GridFieldEditButton::create());
        $this->addComponent(new GridFieldArchiveAction());
        $this->addComponent(new GridFieldUnpublishAction());
        $this->addComponent(new GridFieldPublishAction());
        $this->addComponent(GridField_ActionMenu::create());
        $this->addComponent(GridFieldPageCount::create('toolbar-header-right'));
        $this->addComponent($pagination = GridFieldPaginator::create($itemsPerPage));
        $this->addComponent(GridFieldDetailForm::create(null, $showPagination, $showAdd));
        $this->addComponent(GridFieldOrderableRows::create('Sort'));
        $this->addComponent(GridFieldTitleHeader::create());

        $filter->setThrowExceptionOnBadDataType(false);
        $pagination->setThrowExceptionOnBadDataType(false);

        $this->extend('updateConfig');
    }
}
