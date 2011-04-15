<?php

  namespace DeBaasMedia\Bundle\ContactBundle\Model;

  /**
   * ContactRequest.
   *
   * @author  Marijn Huizendveld <marijn.huizendveld@gmail.com>
   */
  class ContactRequest
  {

    /**
     * @assert:NotBlank(message = "U heeft uw naam niet opgegeven.")
     * @assert:MinLength(limit   = 2
     *                  ,message = "De naam die u heeft opgegeven bestaat uit minder dan twee karakters." 
     *                  )
     */
    public $name;

    /**
     * @assert:NotBlank(message = "U heeft uw e-mail adres niet opgegeven.")
     * @assert:Email(checkMX = true
     *              ,message = "U heeft een niet bestaand e-mail adres opgegeven."
     *              )
     */
    public $emailAddress;

    /**
     * @assert:NotBlank(message = "U heeft het onderwerp van uw contact verzoek niet opgegeven.")
     * @assert:MinLength(limit   = 50
     *                  ,message = "Gelieve het onderwerp van uw contact verzoek te beschrijven in minimaal 50 karakters."
     *                  )
     */
    public $message;

  }