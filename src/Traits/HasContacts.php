<?php namespace drkwolf\Larauser\Traits;

use Illuminate\Support\Arr;

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
        $this->fillable[]        = 'contact';
    }

    private function contactAttributes($name) {
        $attributes     = config('larauser.contact.attributes', []);
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
    | default Contact Attribute
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

    public function getDefaultAddressAttribute() {
        return Arr::get($this->contacts, $this->defaultContactName . '.address');
    }

    /**
     * return email attribute or the default email in the default contact object
     */
    public function getDefaultEmailAttribute() {
        return isset($this->attributes['email'])
            ? $this->attributes['email']
            : Arr::get($this->contacts, $this->defaultContactName . '.email', null);
    }

    /*
    |---------------------------------------------------------------------
    | Credential
    |---------------------------------------------------------------------
    */
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

    private function contactAsObject($phone) {
        return [
            'prefix' => substr($phone, 0, $split_nbr),
            'suffix' => substr($phone, $split_nbr),
            'number' => $phone
        ];
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

    protected function updateContactsFieldAttribute($name, $field, $value) {
        $path = $name . '.' . $field;
        $contacts = $this->contacts;
        Arr::set($contacts, $path, $value);
        $this->contacts = $contacts;
    }

    protected function updateContactsAttribute($path, $value) {
        $contacts = $this->contacts;
        $value = $this->formatContact($value);
        Arr::set($contacts, $path, $value);
        $this->contacts = $contacts;
    }

    public function formatContact($contact) {
        foreach ($contact as $item => $value ) {
            if ($item === 'phone') {
                $contact['phone']['prefix'] = str_replace(' ', '', $value['prefix'] );
                $contact['phone']['suffix'] = str_replace(' ', '', $value['suffix'] );
            }
        }
        return $contact;
    }
}
