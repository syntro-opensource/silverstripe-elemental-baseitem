<?php

namespace Syntro\SilverStripeElementalBaseitem\Forms;

use SilverStripe\Forms\GridField\GridField_ColumnProvider;
use SilverStripe\Forms\GridField\GridField_ActionProvider;
use SilverStripe\Forms\GridField\GridField_ActionMenuItem;
use SilverStripe\Forms\GridField\GridField_FormAction;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Control\Controller;

/**
 * Custom action to unpublish a published block
 *
 * @author Matthias Leutenegger <hello@syntro.ch>
 */
class GridFieldUnpublishAction implements GridField_ColumnProvider, GridField_ActionProvider, GridField_ActionMenuItem
{
    /**
     * getTitle
     *
     * @param  GridField $gridField  the field
     * @param  mixed     $record     the record
     * @param  string    $columnName the column name
     * @return string
     */
    public function getTitle($gridField, $record, $columnName)
    {
        return _t(__CLASS__ . '.UNPUBLISH', 'Unpublish');
    }


    /**
     * getGroup
     *
     * @param  GridField $gridField  the field
     * @param  mixed     $record     the record
     * @param  string    $columnName the column name
     * @return string|null
     */
    public function getGroup($gridField, $record, $columnName)
    {
        $field = $this->getField($gridField, $record);
        return $field ? 'publishactions' : null;
    }

    /**
     * getExtraData
     *
     * @param  GridField $gridField  the field
     * @param  mixed     $record     the record
     * @param  string    $columnName the column name
     * @return array|null
     */
    public function getExtraData($gridField, $record, $columnName)
    {
        $field = $this->getField($gridField, $record);
        if ($field) {
            return $field->getAttributes();
        }

        return null;
    }


    /**
     * augmentColumns - description
     *
     * @param  GridField $gridField the field
     * @param  array     $columns   the columns
     * @return void
     */
    public function augmentColumns($gridField, &$columns)
    {
        if (!in_array('Actions', $columns)) {
            $columns[] = 'Actions';
        }
    }


    /**
     * getColumnAttributes
     *
     * @param  GridField $gridField  the field
     * @param  mixed     $record     the record
     * @param  string    $columnName the column name
     * @return array
     */
    public function getColumnAttributes($gridField, $record, $columnName)
    {
        return ['class' => 'grid-field__col-compact'];
    }


    /**
     * getColumnMetadata
     *
     * @param  GridField $gridField  the field
     * @param  string    $columnName the column name
     * @return array|void
     */
    public function getColumnMetadata($gridField, $columnName)
    {
        if ($columnName === 'Actions') {
            return ['title' => ''];
        }
    }


    /**
     * getColumnsHandled - description
     *
     * @param  GridField $gridField the field
     * @return array
     */
    public function getColumnsHandled($gridField)
    {
        return ['Actions'];
    }


    /**
     * getColumnContent - description
     *
     * @param  GridField $gridField  the field
     * @param  mixed     $record     the record
     * @param  string    $columnName the column name
     * @return string|null
     */
    public function getColumnContent($gridField, $record, $columnName)
    {

        $field = $this->getField($gridField, $record);
        if ($field) {
            return $field->Field();
        }
        return null;
    }

    /**
     * getField - returns the field which is rendered
     *
     * @param  GridField $gridField the field
     * @param  mixed     $record    the record
     * @return GridField_FormAction|null
     */
    public function getField($gridField, $record)
    {
        if (!$record->canEdit() || !$record->isPublished()) {
            return null;
        }
        $title = _t(__CLASS__ . '.UNPUBLISH', 'Unpublish');
        $field = GridField_FormAction::create(
            $gridField,
            'Unpublish' . $record->ID,
            false,
            "dounpublish",
            [
                'RecordID' => $record->ID
            ]
        )
        ->addExtraClass('action--unpublish btn--icon-md font-icon-eye-with-line btn--no-text grid-field__icon-action action-menu--handled')
        ->setAttribute('classNames', 'action--unpublish font-icon-eye-with-line')
        // ->addExtraClass('btn btn--no-text grid-field__icon-action action-menu--handled')
        ->setDescription($title)
        ->setAttribute('aria-label', $title);

        return $field;
    }

    /**
     * getActions
     *
     * @param  GridField $gridField the gridfield
     * @return array
     */
    public function getActions($gridField)
    {
        return ['dounpublish'];
    }


    /**
     * handleAction - description
     *
     * @param  GridField $gridField  the field
     * @param  string    $actionName the name of the action
     * @param  array     $arguments  optional arguments
     * @param  array     $data       the data
     * @return void
     */
    public function handleAction(GridField $gridField, $actionName, $arguments, $data)
    {
        if ($actionName !== 'dounpublish') {
            return;
        }
        /** @var \SilverStripe\ORM\DataList */
        $list = $gridField->getList();
        $item = $list->byID($arguments['RecordID']);
        $item->doUnpublish();

        // output a success message to the user
        Controller::curr()->getResponse()->setStatusCode(
            200,
            _t(__CLASS__ . '.SUCCESS', 'Successfully unpublished item "{title}"', [
                'title' => $item->Title ? $item->Title : $item->ID
            ])
        );
    }
}
