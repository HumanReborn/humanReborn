<?php

namespace EIP\HRBundle\Entity;

/**
 * \class SchemaView
 * \brief Passed to the view, contains informations about schemas (Unit/Building/Technology)
 */
class SchemaView implements \JsonSerializable {

    private $schema; /**< this schema could be HRUnitSchema, HRBuildingSchema, HRTechnologySchema, ...*/
    private $available; /**< boolean which represents if the schema is available*/
    private $errors; /**< string list of encountered errors*/

    /**
     * @param HR*Schema $schema
     * @param boolean $available
     * @param string[] $errors
     */
    public function __construct($schema, $available, $errors) {
        $this->schema = $schema;
        $this->available = $available;
        $this->errors = $errors;
    }

    /**
     * Get schema
     *
     * @return HR*Schema
     */
    public function getSchema() {
        return $this->schema;
    }

    /**
     * Set schema
     *
     * @param HR*Schema $schema
     */
    public function setSchema($schema) {
        $this->schema = $schema;
    }

    /**
     * Get available
     *
     * @return boolean
     */
    public function getAvailable() {
        return $this->available;
    }

    /**
     * Set available
     *
     * @param boolean $available
     */
    public function setAvailable($available) {
        $this->available = $available;
    }

    /**
     * Get errors
     *
     * @return string[]
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Set errors
     *
     * @param string[] $errors
     */
    public function setErrors($errors) {
        $this->errors = $errors;
    }

    public function getBuildingErrors()
    {
        if (!$this->errors || !array_key_exists('buildings', $this->errors))
            return null;
        return $this->errors['buildings'];
    }

    public function getTechnologyErrors()
    {
        if (!$this->errors || !array_key_exists('technologies', $this->errors))
            return null;
        return $this->errors['technologies'];
    }

    /**
     * Get missingRequirements
     * \brief return an array containing the missing requirements for HR*Schema
	 *
	 * @param array $infos
     * @return array
     */
    private static function getMissingRequirements($infos) {
        list($buildingSchemas, $technologySchemas,
                $schema, $playerBuildingScore,
                $playerTechnologyScore) = $infos;

        $missings = array('buildings' => array(), 'technologies' => array());
        // chcks for missing buildings
        foreach ($buildingSchemas as $tmpSchema)
        {
            if (($schema->getBuildingRequirement() & $tmpSchema->getRValue()) == $tmpSchema->getRValue()
                    && ($tmpSchema->getRValue() & $playerBuildingScore) == 0)
            {
                $missings['buildings'][] = $tmpSchema;
            }
        }
        // check for missing technologies
        foreach ($technologySchemas as $tmpSchema)
        {
            if (($schema->getTechnologyRequirement() & $tmpSchema->getRValue()) == $tmpSchema->getRValue()
                    && ($tmpSchema->getRValue() & $playerTechnologyScore) == 0)
            {
                $missings['technologies'][] = $tmpSchema;
            }
        }
        return $missings;
    }

    /**
     * Get schemaViewFromSchema
     * \brief return a SchemaView corresponding to a HR*Schema
	 *
	 * @param array $infos
     * @return SchemaView
     */
    public static function getSchemaViewFromSchema($infos) {
        list($buildingSchemas, $technologySchemas,
                $schema, $playerBuildingScore,
                $playerTechnologyScore) = $infos;
        $available = 0;

        if ($schema->checkBuildingRequirement($playerBuildingScore))
            $available++;
        if ($schema->checkTechnologyRequirement($playerTechnologyScore))
            $available++;
        if ($available == 2)
            return new SchemaView($schema, true, null);
        else
            $missingRequirements = self::getMissingRequirements($infos);
        return new SchemaView($schema, false, $missingRequirements);

    }

    public function jsonSerialize() {
        $e = null;
        if ($this->errors)
        {
            $e = array('buildings' => array(), 'technologies' => array());
            foreach ($this->errors['buildings'] as $error)
                $e['buildings'][] = json_encode($error);
            foreach ($this->errors['technologies'] as $error)
                $e['technologies'][] = json_encode($error);
        }
        return array(
            'schema' => json_encode($this->schema),
            'errors' => $e,
            'available' => $this->available,
        );

    }

}
