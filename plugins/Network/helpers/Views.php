<?php

/**
 * @package     omeka
 * @subpackage  network
 * @copyright   2014 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


/**
 * Compile item metadata for the lazy-loaded `item_body` record attribute.
 *
 * @param Item $item The parent item.
 * @param NetworkRecord|null $record The record.
 * @return string The item metadata.
 */
function in_getItemMarkup($item, $record=null)
{

    // Set the item on the view.
    set_current_record('item', $item);

    if (!is_null($record)) {

        // Get exhibit slug and tags.
        $slug = $record->getExhibit()->slug;
        $tags = in_explode($record->tags);

        // First, try to render an exhibit-specific `item-[tag].php` template.

        foreach ($tags as $tag) { try {
            return get_view()->render(
                'exhibits/themes/'.$slug.'/item-'.$tag.'.php'
            );
        } catch (Exception $e) {}}

        // Next, try to render an exhibit-specific `item.php` template.

        try {
            return get_view()->render(
                'exhibits/themes/'.$slug.'/item.php'
            );
        } catch (Exception $e) {}

    }

    // Default to the global `item.php` template, which is included in the
    // core plugin and can also be overridden in the public theme:

    return get_view()->render('exhibits/item.php');

}


/**
 * Returns a link to a Network exhibit.
 *
 * @param NetworkExhibit|null $exhibit The exhibit record.
 * @param string $action The action for the link.
 * @param string $text The link text.
 * @param array $props Array of properties for the element.
 * @return string The HTML link.
 */
function in_getExhibitLink(
    $exhibit, $action, $text, $props=array(), $public=true)
{

    // Get exhibit and link text.
    $exhibit = $exhibit ? $exhibit : in_getExhibit();
    $text = $text ? $text : in_getExhibitField('title');

    $identifier = $exhibit->id;


    // Construct the exhibit route.
    $route = 'network/'.$action.'/'.$identifier;
    $props['href'] = $public ? public_url($route) : url($route);

    // Return the anchor tag.
    return '<a '.tag_attributes($props).'>'.$text.'</a>';

}

/**
 * Returns a link to a Network exhibit.
 *
 * @param NetworkExhibit|null $exhibit The exhibit record.
 * @param string $action The action for the link.
 * @return string The URL.
 */
function in_getExhibitUrl($exhibit, $action, $public=true)
{
    $exhibit = $exhibit ? $exhibit : in_getExhibit();

    $identifier = $exhibit->id;

    $route = 'network/'.$action.'/'.$identifier;
    $href  = $public ? public_url($route) : url($route);

    return $href;
}

/**
 * Count the records in an exhibit.
 *
 * @param NetworkExhibit $exhibit The exhibit record.
 * @return integer The number of records.
 */
function in_getExhibitRecordCount($exhibit=null)
{
    $exhibit = $exhibit ? $exhibit : in_getExhibit();
    return (int) $exhibit->getNumberOfRecords();
}


/**
 * Render and return the exhibit partial.
 *
 * @return string The exhibit markup.
 */
function in_getExhibitMarkup()
{
    return get_view()->partial('exhibits/partials/exhibit.php');
}


/**
 * Return specific field for a network record.
 *
 * @param string $fieldname The model attribute.
 * @param NetworkExhibit $exhibit The exhibit.
 * @return string The field value.
 */
function in_getExhibitField($fieldname, $exhibit=null)
{
    $exhibit = $exhibit ? $exhibit : in_getExhibit();
    return $exhibit->$fieldname;
}


/**
 * Have any exhibits been created?
 *
 * @return boolean
 */
function in_exhibitsHaveBeenCreated()
{
    return count(get_view()->network_exhibits);
}


/**
 * Returns the current exhibit.
 *
 * @return NetworkExhibit|null
 */
function in_getExhibit()
{
    return get_view()->network_exhibit;
}
