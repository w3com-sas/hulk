<?php

namespace W3com\HulkBundle\Service;

use W3com\BoomBundle\Exception\EntityNotFoundException;

class DisplayCachingManager
{
    const DISPLAY_CACHE_DIRECTORY = '../var/cache/display/';

    private $boom;
    private $listeDisplay = [];

    public function __construct(
        BoomManager $boom
    )
    {
        $this->boom = $boom;

        if(!is_dir(self::DISPLAY_CACHE_DIRECTORY)){
            mkdir(self::DISPLAY_CACHE_DIRECTORY);
        }
    }

    public function treat($displayName = '')
    {
        if($displayName !== '' && !in_array($displayName,$this->listeDisplay)){
            throw new \Exception('Display non prévu dans le traitement, veuillez vérifier la configuration');
        }
        if($displayName !== ''){
            $this->build($displayName);
        } else {
            foreach($this->listeDisplay as $display){
                $this->build($display);
            }
        }
    }

    private function build($displayName)
    {
        // On récupère la première ligne pour avoir la liste des champs
        try {
            $repo = $this->boom->getRepository($displayName);

            $params = $repo->createParams();
            $params->setTop(1);
            $firstLine = $repo->findAll($params);


        } catch(EntityNotFoundException $e){
            throw new \Exception('Entity not found '.$e->getMessage());
        } catch(\Exception $e){
            throw new \Exception('Erreur in build : '.$e->getMessage());
        }
    }
}
