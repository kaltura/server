<?php

class kThumbnailParameters
{
    /**
     * @var bool
     */
    private $supportAnimatedThumbnail;
    
    public function getSupportAnimatedThumbnail()
    {
        return $this->supportAnimatedThumbnail;
    }
    
    
    public function setSupportAnimatedThumbnail($support)
    {
        $this->supportAnimatedThumbnail = $support;
    }
}