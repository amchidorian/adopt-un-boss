<?php

/**
 * Created by PhpStorm.
 * User: vidalfrancois
 * Date: 02/07/2018
 * Time: 09:53
 */

namespace BWB\Framework\mvc\controllers;

use BWB\Framework\mvc\Controller;
use BWB\Framework\mvc\controllers\SecurityController;
use BWB\Framework\mvc\dao\DAOCandidat;
use BWB\Framework\mvc\dao\DAOEntreprise;
use BWB\Framework\mvc\dao\DAOEvent;
use BWB\Framework\mvc\dao\DAOOffre;
use BWB\Framework\mvc\dao\DAOUser;
use BWB\Framework\mvc\SecurityMiddleware;

class HomeController extends Controller {

    private $dao_event;
    private $dao_offre;
    private $dao_candidat;
    private $dao_entreprise;
    private $dao_user;
    private $security_middleware;
    private $security_controller;

    /**
     * HomeController constructor.
     * @param $dao_event
     * @param $dao_offres
     * @param $dao_candidats
     * @param $dao_entreprises
     */
    public function __construct() {
        parent::__construct();

        $this->dao_event = new DAOEvent();
        $this->dao_user = new DAOUser();
        $this->dao_offre = new DAOOffre();
        $this->dao_candidat = new DAOCandidat();
        $this->dao_entreprise = new DAOEntreprise();
        $this->security_middleware = new SecurityMiddleware();
        $this->security_controller = new SecurityController();
    }

    public function get_id() {
        return $this->security_middleware->verifyToken($_COOKIE['tkn'])->id;
    }

    public function get_role() {
        return $this->security_middleware->verifyToken($_COOKIE['tkn'])->role;
    }

    public function get_view() {
        $events = $this->dao_event->get_event_valide();
        $offres = $this->dao_offre->get_new_offre();
        $candidats = $this->dao_candidat->get_new_candidat();
        $entreprises = $this->dao_entreprise->get_new_entreprise();
        if (isset($_COOKIE['tkn'])):
            $user = $this->dao_user->retrieve_user($this->get_role(), $this->get_id());
            $this->render("home", array(
                "user" => $user,
                "events" => $events,
                "offres" => $offres,
                "candidats" => $candidats,
                "entreprises" => $entreprises,
                "caca"=>true
            ));
        else:
            $this->render("home", array(
                "events" => $events,
                "offres" => $offres,
                "candidats" => $candidats,
                "entreprises" => $entreprises
            ));
        endif;
    }

}
