<?php
namespace Api\V8\JsonApi\Response;

class ErrorResponse implements \JsonSerializable
{
    /**
     * @var int
     */
    private $status;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $detail;

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDetail()
    {
        return $this->detail;
    }

    /**
     * @param string $detail
     */
    public function setDetail($detail)
    {
        $this->detail = $detail;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        $response = [
            'errors' => [
                'status' => $this->getStatus(),
                'title' => $this->getTitle(),
                'detail' => $this->getDetail(),
            ]
        ];

        return array_filter($response);
    }
}
