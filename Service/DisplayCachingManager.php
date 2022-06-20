<?php

namespace W3com\HulkBundle\Service;

use W3com\BoomBundle\Exception\EntityNotFoundException;
use W3com\BoomBundle\Service\BoomManager;

class DisplayCachingManager
{
    const DISPLAY_CACHE_DIRECTORY = 'var/cache/display/';
    const MAX_POSSIBLE_RESULT = 5000;

    private $boom;
    private $config;
    private $listCachingDisplay;
    private $absolutePathCache;

    public function __construct(
        BoomManager $boom,
        $config
    )
    {
        $this->boom = $boom;
        $this->config = $config;
        $this->listCachingDisplay = array_key_exists('hulk_list_caching_display',$this->config) ?
            explode(';',$this->config['hulk_list_caching_display']):
            [];
        $this->absolutePathCache = __DIR__.'/../../../../'.self::DISPLAY_CACHE_DIRECTORY;

        if(!is_dir($this->absolutePathCache)){
            $result = mkdir($this->absolutePathCache);
        }
    }

    public function treat($displayName = '')
    {
        if($displayName !== '' && !in_array($displayName,$this->listCachingDisplay)){
            throw new \Exception('Display non prévu dans le traitement, veuillez vérifier la configuration');
        }
        if($displayName !== ''){
            $this->build($displayName);
        } else {
            foreach($this->listCachingDisplay as $display){
                $this->build($display);
            }
        }
    }

    private function build($displayName)
    {
        // Tout d'abord on créé le dossier
        if(!is_dir($this->absolutePathCache.$displayName)){
            mkdir($this->absolutePathCache.$displayName);
        }

        // On récupère la première ligne pour avoir la liste des champs
        try {
            $repo = $this->boom->getRepository($displayName);

            $params = $repo->createParams();
            $params->setTop(1);
            $firstLine = $repo->findAll($params);

            $structure = array_keys($firstLine[0]->getChangedFields());

            foreach($structure as $columnName){
                $nbDistinctValues = $this->buildColumn($repo, $displayName,$columnName);
            }
        } catch(EntityNotFoundException $e){
            throw new \Exception('Entity not found '.$e->getMessage());
        } catch(\Exception $e){
            throw new \Exception('Erreur in build : '.$e->getMessage());
        }
    }

    private function buildColumn($repo, $displayName, $columnName)
    {
        $params = $repo->createParams();
        $params->addSelect($columnName);
        $nbDistinctValues = $repo->count($params);

        // On réalise ici la conversion entre le nom de la propriété et le nom de la
        // propriété
        // On est obligé de faire cela car dans la définition du filtre on ne connait
        // pas le nom de la propriété de l'entité
        $fileNameColumnPath = $this->absolutePathCache.$displayName.'/'.ucfirst($columnName);

        $selectValues = [];
        if($nbDistinctValues < self::MAX_POSSIBLE_RESULT){
            $listeValeur = $repo->findAll($params);
            if(count($listeValeur) > 0){
                foreach($listeValeur as $entity){
                    $selectValues[] = $entity->get($columnName);
                }
            }
        }
        file_put_contents($fileNameColumnPath,serialize($selectValues));

        return $nbDistinctValues;
    }

    public function cacheIsActivated($displayName)
    {
        return is_dir($this->absolutePathCache.$displayName);
    }

    public function columCacheIsPresent($displayName, $columnName)
    {
        if(!is_file($this->absolutePathCache.$displayName.'/'.$columnName)){
            return false;
        }
        $liste = unserialize(file_get_contents($this->absolutePathCache.$displayName.'/'.$columnName));
        return is_array($liste) && count($liste) > 0;
    }

    /**
     * Return null if no interisting cache present
     * @param $displayName
     * @param $columnName
     * @return mixed|null
     */
    public function getCacheColumn($displayName, $columnName)
    {
        if(!$this->columCacheIsPresent($displayName, $columnName)){
            return null;
        }
        return (unserialize(file_get_contents($this->absolutePathCache.$displayName.'/'.$columnName)));
    }
}
