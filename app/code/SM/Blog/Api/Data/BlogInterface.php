<?php


namespace SM\Blog\Api\Data;


interface BlogInterface
{

    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID                  = 'block_id';
    const SHORT_DESCRIPTION   = 'short_description';
    const DESCRIPTION         = 'description';
    const STATUS              = 'status';
    const THUMBNAIL           = 'thumbnail';
    const PUBLISH_DATE_FROM   = 'publish_date_from';
    const PUBLISH_DATE_TO     = 'publish_date_to';
    const URL_KEY             = 'url_key';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get identifier
     *
     * @return string
     */
    public function getShortDescription();

    /**
     * Get title
     *
     * @return string|null
     */
    public function getDescription();

    /**
     * Get content
     *
     * @return string|null
     */
    public function getThumbnail();

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getPublishDateFrom();

    /**
     * Get update time
     *
     * @return string|null
     */
    public function getPublishDateTo();

    /**
     * Is active
     *
     * @return bool|null
     */
    public function getStatus();

    /**
     * Set ID
     *
     * @param int $id
     * @return BlogInterface
     */
    public function setId($id);

    /**
     * Set identifier
     *
     * @param string $shortDescription
     * @return BlogInterface
     */
    public function setShortDescription($shortDescription);

    /**
     * Set title
     *
     * @param string $description
     * @return BlogInterface
     */
    public function setDescription($description);

    /**
     * Set content
     *
     * @param string $thumbnail
     * @return BlogInterface
     */
    public function setThumbnail($thumbnail);

    /**
     * Set creation time
     *
     * @param string $publishDateFrom
     * @return BlogInterface
     */
    public function setPublishDateFrom($publishDateFrom);

    /**
     * Set update time
     *
     * @param string $publishDateTo
     * @return BlogInterface
     */
    public function setPublishDateTo($publishDateTo);

    /**
     * Set is active
     *
     * @param bool|int $status
     * @return BlogInterface
     */
    public function setStatus($status);
}
