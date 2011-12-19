<?php

    function get_position_type( $detail ) {
        if( Session::newInstance()->_getForm('pj_positionType') != '' ) {
            return Session::newInstance()->_getForm( 'pj_positionType' ) ;
        }

        if( key_exists('e_position_type', $detail) ) {
            return $detail['e_position_type'] ;
        }

        return '' ;
    }

    function get_salary( $detail ) {
        if( Session::newInstance()->_getForm('pj_salary') != '' ) {
            return Session::newInstance()->_getForm( 'pj_salary' ) ;
        }

        if( key_exists( 's_salary', $detail ) ) {
            return $detail['s_salary'] ;
        }

        return '' ;
    }

    function get_contract( $detail, $locale ) {
        if( Session::newInstance()->_getForm( 'pj_contract' ) != '' ) {
            $contract = Session::newInstance()->_getForm( 'pj_contract' ) ;

            if( !is_array( $contract ) ) {
                $contract = array() ;
            }

            if( key_exists( $locale, $contract ) ) {
                return $contract[$locale] ;
            }
        }

        if( key_exists( 'locale', $detail ) ) {
            if( key_exists( $locale, $detail['locale'] ) ) {
                return $detail['locale'][$locale]['s_contract'] ;
            }
        }

        return '' ;
    }

    function get_studies( $detail, $locale ) {
        if( Session::newInstance()->_getForm( 'pj_studies' ) != '' ) {
            $studies = Session::newInstance()->_getForm( 'pj_studies' ) ;

            if( !is_array( $studies ) ) {
                $studies = array() ;
            }

            if( key_exists( $locale, $studies ) ) {
                return $studies[$locale] ;
            }
        }

        if( key_exists( 'locale', $detail ) ) {
            if( key_exists( $locale, $detail['locale'] ) ) {
                return $detail['locale'][$locale]['s_studies'] ;
            }
        }

        return '' ;
    }

    function get_experience( $detail, $locale ) {
        if( Session::newInstance()->_getForm( 'pj_experience' ) != '' ) {
            $experience = Session::newInstance()->_getForm( 'pj_experience' ) ;

            if( !is_array( $experience ) ) {
                $experience = array() ;
            }

            if( key_exists( $locale, $experience ) ) {
                return $experience[$locale] ;
            }
        }

        if( key_exists( 'locale', $detail ) ) {
            if( key_exists( $locale, $detail['locale'] ) ) {
                return $detail['locale'][$locale]['s_experience'] ;
            }
        }

        return '' ;
    }

    function get_requirements( $detail, $locale ) {
        if( Session::newInstance()->_getForm( 'pj_requirements' ) != '' ) {
            $requirements = Session::newInstance()->_getForm( 'pj_requirements' ) ;

            if( !is_array( $requirements ) ) {
                $requirements = array() ;
            }

            if( key_exists( $locale, $requirements ) ) {
                return $requirements[$locale] ;
            }
        }

        if( key_exists( 'locale', $detail ) ) {
            if( key_exists( $locale, $detail['locale'] ) ) {
                return $detail['locale'][$locale]['s_requirements'] ;
            }
        }

        return '' ;
    }

    function get_company_name( ) {
        return osc_get_preference( 'company', 'jobboard' ) ;
    }

?>