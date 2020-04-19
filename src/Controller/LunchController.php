<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Finder\Finder;

class LunchController extends AbstractController{
    /**
     * @Route("/lunch")
     * @Method({"GET"})
     */
    public function index(Request $req){
        
        $statusCode = 200;
        $items       = array();

        $finder = new Finder();
        $finder->in(dirname(__DIR__, 1)."/App/Ingredient")->in(dirname(__DIR__, 1)."/App/Recipe");
        
        $contentIngredient  = array();
        $contentRecipes     = array();
        $fileContent        = array();

        foreach ($finder as $file) {
            
            $fileContent = json_decode($file->getContents(),true);
            if(isset($fileContent['ingredients'])){
                $contentIngredient = $fileContent;
            }
            if(isset($fileContent['recipes'])){
                $contentRecipes = $fileContent;
            }
        }

        $strCurrTime = 0;
        
        if($this->validateDate($req->query->get('date'))){
            $strCurrTime = strtotime($req->query->get('date'));
        }
        else if($req->query->get('date')){
            $statusCode = 400;
            $items["message"] = "date has wrong format";   
        }else{
            $strCurrTime = strtotime(date("Y-m-d"));
        }

        $counterArr = 0;

        if($strCurrTime){
            $recipeReturn = array(); 

            foreach($contentRecipes['recipes'] as $recipe){
            
                $recipeIngredients = $recipe["ingredients"];
                $bestBefore = 0;
    
                foreach($contentIngredient['ingredients'] as $ingredient){
                                    
                    if( $strCurrTime <= strtotime($ingredient["use-by"]) ){                    
    
                        if (($key = array_search($ingredient["title"], $recipeIngredients)) !== false) {
                            
                            if($bestBefore <  strtotime($ingredient["best-before"]) ){
                                $bestBefore = $ingredient["best-before"];
                                
                            }
    
                            unset($recipeIngredients[$key]);
                        }
                    }
                }
                
                if(count($recipeIngredients) == 0){
                    array_push($recipeReturn,$recipe);
                    $recipeReturn[$counterArr]['order']         = strtotime($bestBefore);
                    $recipeReturn[$counterArr]['best-before']   = $bestBefore;                
                    $counterArr++;
                }
            }
            $items["recipes"] = $recipeReturn;
            if(count($recipeReturn)){
                usort($items,array($this,'sorting'));                            
            }                
            else{
                $items["message"] = "No data";
            }
                
            
        }
        
        $response = new JsonResponse($items,$statusCode);
        return $response;   
    }

    private function validateDate($date)
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);        
        return $d && $d->format('Y-m-d') === $date;
    }

    private function sorting( $a, $b ) { 
        if(  $a['order'] ==  $b['order'] ){ return 0 ; } 
        return ($a['order'] > $b['order']) ? -1 : 1;
    }

}
