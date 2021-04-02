<?php

namespace Syntro\SilverStripeElementalBaseitem\Forms;

use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldButtonRow;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldToolbarHeader;
use SilverStripe\Forms\GridField\GridFieldFilterHeader;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridField_ActionMenu;
use SilverStripe\Forms\GridField\GridFieldPageCount;
use SilverStripe\Forms\GridField\GridFieldPaginator;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Versioned\GridFieldArchiveAction;
use SilverStripe\Versioned\VersionedGridFieldState\VersionedGridFieldState;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

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

        $this->addComponent(new GridFieldButtonRow('before'));
        $this->addComponent(new GridFieldAddNewButton('buttons-before-left'));
        $this->addComponent(new GridFieldToolbarHeader());
        $this->addComponent($filter = new GridFieldFilterHeader());
        $this->addComponent(new GridFieldDataColumns());
        $this->addComponent(new VersionedGridFieldState());
        $this->addComponent(new GridFieldEditButton());
        $this->addComponent(new GridFieldArchiveAction());
        $this->addComponent(new GridFieldUnpublishAction());
        $this->addComponent(new GridFieldPublishAction());
        $this->addComponent(new GridField_ActionMenu());
        $this->addComponent(new GridFieldPageCount('toolbar-header-right'));
        $this->addComponent($pagination = new GridFieldPaginator($itemsPerPage));
        $this->addComponent(new GridFieldDetailForm(null, $showPagination, $showAdd));
        $this->addComponent(new GridFieldOrderableRows('Sort'));

        $filter->setThrowExceptionOnBadDataType(false);
        $pagination->setThrowExceptionOnBadDataType(false);

        $this->extend('updateConfig');
    }
}
