<?php

namespace pringuin\SearchreplaceBundle\Controller;

use Pimcore\Controller\FrontendController;
use Pimcore\Db;
use Pimcore\Tool\Authentication;
use pringuin\SearchreplaceBundle\Helper\Configurationhelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends FrontendController
{

    public function onKernelController(FilterControllerEvent $event)
    {
        // set auto-rendering to twig
        $this->setViewAutoRender($event->getRequest(), true, 'twig');
    }

    /**
     * @Route("/pringuin_searchreplace")
     */
    public function indexAction(Request $request)
    {
        $user = Authentication::authenticateSession($request);
        if(!$user){
            throw new AccessDeniedHttpException();
        }

        $allowedreplacetypes = ['input','textarea','wysiwyg'];

        $responsemessage = '';
        if($request->isMethod('post')){

            if($request->request->get('searchterm') && $request->request->get('replaceterm') && is_iterable($request->request->get('searchinfields'))){
                foreach ($request->request->get('searchinfields') as $searchinfields){
                    if(in_array($searchinfields,$allowedreplacetypes)){
                        $sql = "SELECT * from documents_elements where`type` = :type AND `data` LIKE CONCAT('%', :data, '%')";
                        $stmt = Db::get()->prepare($sql);
                        $stmt->bindParam(':type', $searchinfields, \PDO::PARAM_STR);
                        $stmt->bindParam(':data', $request->request->get('searchterm'), \PDO::PARAM_STR);
                        $stmt->execute();
                        $result = $stmt->fetchAll();

                        if(is_array($result) && is_iterable($result) && count($result) > 0){
                            if($request->request->get('testrun') && $request->request->get('testrun') == 'yes'){
                                $responsemessage .= '<div>'.$this
                                        ->get('translator')
                                        ->trans(
                                            'results_found_in_testrun',
                                            [],
                                            'admin'
                                        ).'</div>';
                                foreach($result as $foundelement){
                                    if(key_exists('data',$foundelement)) {
                                        $responsemessage .= '<div><h2>'.$this
                                                ->get('translator')
                                                ->trans(
                                                    'before',
                                                    [],
                                                    'admin'
                                                ).' <span onclick="goToDocument('.$foundelement['documentId'].')" style="padding-top:5px;background-color:#000000">
                                                <img src="/bundles/pimcoreadmin/img/flat-white-icons/page.svg" />
                                                </span></h2>';
                                        $responsemessage .= strip_tags($foundelement['data']);
                                        $responsemessage .= '</div>';
                                        $html_pattern = $this->make_html_pattern($request->request->get('searchterm'));
                                        $text_replacement = $this->make_text_replacement($request->request->get('replaceterm'));
                                        $responsemessage .= '<div><h2>'.$this
                                                ->get('translator')
                                                ->trans(
                                                    'after',
                                                    [],
                                                    'admin'
                                                ).'</h2>';
                                        $responsemessage .= strip_tags(preg_replace($html_pattern, $text_replacement, $foundelement['data']));
                                        $responsemessage .= '</div>';
                                    }
                                }
                            }
                            else{
                                foreach($result as $foundelement) {
                                    if (key_exists('data', $foundelement)) {
                                        $html_pattern = $this->make_html_pattern($request->request->get('searchterm'));
                                        $text_replacement = $this->make_text_replacement($request->request->get('replaceterm'));
                                        $replacement = preg_replace($html_pattern, $text_replacement, $foundelement['data']);
                                        $updatesuccess = true;
                                        try{
                                            $data = [
                                                'data' => $replacement,
                                                'documentId' => $foundelement['documentId'],
                                                'name' => $foundelement['name'],
                                                'type' => $searchinfields,
                                            ];
                                            $sql = "UPDATE documents_elements SET data=:data WHERE documentId=:documentId AND name=:name and type=:type";
                                            $ustmt = Db::get()->prepare($sql);
                                            $ustmt->execute($data);
                                        }
                                        catch (\Exception $e){
                                            $updatesuccess = false;
                                        }
                                        if($updatesuccess) {
                                            $responsemessage .= '<div>' . $this
                                                ->get('translator')
                                                ->trans(
                                                    'text_replaced_successfully',
                                                    [],
                                                    'admin'
                                                ) . ' ' . $searchinfields . '</div>';
                                        }
                                        else{
                                            $responsemessage .= '<div>'.$this
                                                ->get('translator')
                                                ->trans(
                                                    'text_replacement_failed',
                                                    [],
                                                    'admin'
                                                ).' '.$searchinfields.'</div>';
                                        }
                                    }
                                }
                            }
                        }
                        else{
                            $responsemessage .= '<div>'.$this
                                    ->get('translator')
                                    ->trans(
                                        'no_results_found',
                                        [],
                                        'admin'
                                    ).' '.$searchinfields.'</div>';
                        }

                    }

                }
            }
            else{
                $responsemessage .= '<div>'.$this
                        ->get('translator')
                        ->trans(
                            'required_field_missing',
                            [],
                            'admin'
                        ).'</div>';
            }


        }

        $this->view->message = $responsemessage;

        //return new Response('Hello world from pringuin_searchreplace');
    }


    private function make_html_pattern($string){
        $patterns = array(
            '!(\w+)!i',
            '#^#',
            '! !',
            '#$#');
        $replacements = array(
            "($1)",
            '!',
            '(\s?<?/?[^>]*>?\s?)',
            '!i');
        $new_string = preg_replace($patterns,$replacements,$string);
        return $new_string;
    }

    private function make_text_replacement($replacement){
        $patterns = array(
            '!^(\w+)(\s+)(\w+)(\s+)(\w+)$!',
            '!^(\w+)(\s+)(\w+)$!');
        $replacements = array(
            '$1\$2$3\$4$5',
            '$1\$2$3');
        $new_replacement = preg_replace($patterns,$replacements,$replacement);
        return $new_replacement;
    }


}
