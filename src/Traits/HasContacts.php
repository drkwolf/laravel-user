<?php namespace drkwolf\Larauser\Traits;

use Illuminate\Support\Arr;
use function GuzzleHttp\json_decode;


/**
 * $this->email and $this->phone are unique and can be used to log as user
 */
trait HasContacts {
    private $defaultContactName;

    public function initializeHasContacts () {
        $this->defaultContactName = config('larauser.contact.default', '_default_');

        $this->attributes['contacts'] = json_encode(
            $this->contactAttributes($this->defaultContactName)
        );
        $this->casts['contacts'] = 'array';
        $this->fillable[]  = 'contact';
    }

    private function contactAttributes($name) {
        $attributes = config('larauser.contact.attributes', []);
        return [ $name => $attributes ];
    }

    public function getCredentialsAttribute () {
        return [
            'username' => $this->username,
            'phone' => $this->attributes['phone'],
            'email' => $this->attributes['email'],
        ];
    }

    /*
    |---------------------------------------------------------------------
    | Contact Attrubute
    |---------------------------------------------------------------------
    */
    
    public function setContactAttribute($value) {
        $this->setDefaultContactAttribute($value);
    }

    public function getContactAttribute() {
        return $this->getDefaultContactAttribute();
    }

    public function setDefaultContactAttribute($value) {
        $this->updateContactsAttribute($this->defaultContactName, $value);
    }

    public function getDefaultContactAttribute() {
        $contacts = $this->contacts ? $this->contacts : json_decode($this->attributes['contacts'],  true);
        return Arr::get($contacts, $this->defaultContactName);
    }

    /*
    |---------------------------------------------------------------------
    | Credential
    |---------------------------------------------------------------------
    */

    /**
     * return email attribute or the default email in the default contact object
     */
    public function getDefaultEmailAttribute() {
        return isset($this->attributes['email'])
            ? $this->attributes['email']
            : Arr::get($this->contacts, $this->defaultContactName . '.email', null);
    }

    private function phoneAsObject($phone) {
        $split_nbr = $phone[0] == '+' ? 3 : 4; // 00## or +##
        return [
            'prefix' => substr($phone, 0, $split_nbr),
            'suffix' => substr($phone, $split_nbr),
            'number' => $phone
        ];
    }

    public function getPhoneStrAttribute() {
        if (isset($this->attributes['phone']) ) {
            return $this->attributes['phone'];
        } else {
            $path =  $this->defaultContactName . 'phone';
            return  Arr::get($this->contacts, $path, null);
        }
    }

    public function getPhoneAttribute() {
        if (isset($this->attributes['phone']) ) {
            return $this->phoneAsObject($this->attributes['phone']);
        } else {
            $path = $this->defaultContactName . '.phone';
            return Arr::get($this->contacts, $path , ['prefix' => '', 'suffix' => '']);
        }
    }

    public function setPhoneAttribute($value) {
        $this->attributes['phone'] = Arr::get($value, 'prefix') . Arr::get($value, 'suffix');
    }

    public function addContactPhone($name, $value) {
        $path = $name . '.phone';
        $this->updateContactsAttribute($path, $value);
    }

    public function addAddress($name, $value) {
        $path = $name . '.address';
        $this->updateContactsAttribute($path, $value);
    }

    public function getDefaultAddressAttribute() {
        return Arr::get($this->contacts, $this->defaultContactName . '.address');
    }

    public function addContactEmail($name, $value) {
        $path = $name . '.email';
        $this->updateContactsAttribute($path, $value);
    }

    protected function updateContactsAttribute($path, $value) {
        $contacts = $this->contacts;
        Arr::set($contacts, $path, $value);
        $this->contacts = $contacts;
    }
}
