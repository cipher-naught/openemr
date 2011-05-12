<?php
class NFQ_0038_Numerator4 implements CqmFilterIF 
{
    public function getTitle() {
        return "Numerator 4";
    }
    
    public function test( CqmPatient $patient, $dateBegin, $dateEnd )
    {
        $options = array( Medication::OPTION_COUNT => 2 );
        $dobPlus42Days = date( 'Y-m-d 00:00:00', strtotime( '+42 day', strtotime( $patient->dob ) ) );
        $dobPlus2Years = date( 'Y-m-d 00:00:00', strtotime( '+2 year', strtotime( $patient->dob ) ) );  
        if ( Helper::checkMed( Medication::HIB, $dobPlus42Days, $dobPlus2Years, $options ) &&
            !Helper::checkAllergy( Allergy::HIB, $patient ) ) {
            return true;
        }
        return false;
    }
}
