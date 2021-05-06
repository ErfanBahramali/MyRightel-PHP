<?php

namespace MyRightelPHP;

class MyRightel
{
    private $phoneNumber;
    private $password;
    private $cookieFile;

    /** 
     * user Account Phone Number
     * @param int $phoneNumber user phoneNumber
     * @param string $password account password
     */
    public function __construct(int $phoneNumber, string $password)
    {
        $this->phoneNumber = $phoneNumber;
        $this->password = $password;
        $this->cookieFile = ".config/{$phoneNumber}_cookie.txt";
        if (!file_exists('.config')) @mkdir('.config');
        if (!file_exists($this->cookieFile)) touch($this->cookieFile);

        if (!$this->checkLogined()) {
            $this->login();
        }
    }

    /** 
     * request to url
     * @param string $url sitr url
     * @param array $parameters optional request parameters
     * @return string request response
     */
    public function request(string $url, array $parameters = [], bool $returnInfo = false)
    {
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ACCEPT_ENCODING => 'gzip, deflate, br',
            CURLOPT_COOKIEJAR => $this->cookieFile,
            CURLOPT_COOKIEFILE => $this->cookieFile,
            CURLOPT_SSL_CIPHER_LIST => 'DEFAULT@SECLEVEL=1',
            CURLOPT_HTTPHEADER => [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; rv:78.0) Gecko/20100101 Firefox/78.0',
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language: en-US,en;q=0.5',
                'Connection: keep-alive',
                // 'Upgrade-Insecure-Requests: 1',
            ],
            // CURLOPT_VERBOSE => true,
        ];

        if (!empty($parameters)) {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = http_build_query($parameters);
            $options[CURLOPT_HTTPHEADER][] = 'X-Requested-With: XMLHttpRequest';
        }
        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        if ($returnInfo) {
            $response = curl_getinfo($ch);
        }
        curl_close($ch);
        return $response;
    }

    /** 
     * check account is logined or no
     * @return bool on success return true on faild return false
     */
    private function checkLogined()
    {
        $response = $this->request('https://my.rightel.ir/fa/home', [], true);
        if (parse_url($response['url'])['path'] !== '/fa/home') {
            return false;
        }
        return true;
    }

    /** 
     * login in to account
     * @return bool on success return true on faild return false
     */
    private function login()
    {
        $this->request('https://my.rightel.ir/c/portal/login', [
            'authType' => 'screenName',
            'login' => $this->phoneNumber,
            'password' => $this->password,
            'rememberMe' => 'false',
            'redirect' => 'https://my.rightel.ir/fa/home',
        ]);
        return $this->checkLogined();
    }

    /** 
     * get new captcha image
     * @return string image captcha content
     */
    private function getNewCaptcha()
    {
        return $this->request('https://my.rightel.ir/login?p_p_id=newlogin_WAR_rightelecareportlet&p_p_lifecycle=2&p_p_state=normal&p_p_mode=view&p_p_resource_id=captcha&p_p_cacheability=cacheLevelPage&p_p_col_id=column-1&p_p_col_count=1');
    }

    /** 
     * if day is Friday get free friday
     * @return array last request response (packageActivationStatus) and gift text
     * if packageActivationStatus equal true so gift geted else gift not geted
     */
    public function getFridayGift()
    {
        $this->request('https://my.rightel.ir/fa/game');
        $response = $this->request('https://my.rightel.ir/fa/game?p_p_id=randompackagegamedisplay_WAR_rightelecareportlet&p_p_lifecycle=0&p_p_state=normal&p_p_mode=view&p_p_col_id=column-1&p_p_col_count=1&_randompackagegamedisplay_WAR_rightelecareportlet_cmd=go-to-game');
        preg_match('/<div id="gift-game">.*?<p class="package-title">(.*?)<\/p>.*?<\/div>/', $response, $matches);
        $gift = $matches[1];
        $response = $this->request('https://my.rightel.ir/fa/game', [
            'p_p_id' => 'randompackagegamedisplay_WAR_rightelecareportlet',
            'p_p_lifecycle' => '2',
            'p_p_state' => 'normal',
            'p_p_mode' => 'view',
            'p_p_resource_id' => 'activate-package',
            'p_p_cacheability' => 'cacheLevelPage',
            'p_p_col_id' => 'column-1',
            'p_p_col_count' => '1',
            '_randompackagegamedisplay_WAR_rightelecareportlet_cmd' => 'go-to-game',
        ]);
        $response = json_decode($response, true);
        $response['gift'] = $gift;
        return $response;
    }

    /** 
     * get (internet,voice,sms,charge) inventory 
     * @return array (internet,voice,sms,charge) inventory 
     */
    public function getInventory()
    {
        $response = $this->request(
            'https://my.rightel.ir/fa/c/portal/render_portlet',
            [
                'p_l_id' => '21909',
                'p_p_id' => 'pakagesconsumptiondisplay_WAR_rightelecareportlet',
                'p_p_lifecycle' => '0',
                'p_t_lifecycle' => '0',
                'p_p_state' => 'normal',
                'p_p_mode' => 'view',
                'p_p_col_id' => 'column-2',
                'p_p_col_pos' => '0',
                'p_p_col_count' => '3',
                'p_p_isolated' => '1',
                'currentURL' => '/fa/home',
                'portletAjaxable' => '1'
            ]
        );

        $renderPortlet = [];
        preg_match('/<div class="gage circle span3 internet"> <a class="content" href=".*?"> <p class="size-text--normal-big color-text--grey-3">(.*?)<\/p> <strong class="size-number--bold-header">(.*?)<span class="size-text--small-then"><\/span> <\/strong> <p class="size-text--bold-big color-text--grey-1">(.*?)<\/p> <\/a> <\/div>/', $response, $internet);
        unset($internet[0]);
        preg_match('/<div class="gage circle span3 voice"> <a class="content" href=".*?"> <p class="size-text--normal-big color-text--grey-3">(.*?)<\/p> <strong class="size-number--bold-header">(.*?)<span class="size-text--small-then"><\/span><\/strong><br> <p class="size-text--bold-big color-text--grey-1">(.*?)<\/p> <\/a> <\/div>/', $response, $voice);
        unset($voice[0]);
        preg_match('/<div class="gage circle span3 sms"> <a class="content" href=".*?"> <p class="size-text--normal-big color-text--grey-3">(.*?)<\/p> <strong class="size-number--bold-header">(.*?)<span class="size-text--small-then"><\/span><\/strong><br> <p class="size-text--bold-big color-text--grey-1">(.*?)<\/p> <\/a> <\/div>/', $response, $sms);
        unset($sms[0]);
        preg_match('/<div class="total-sum span3"> <div class="content"> <strong class="size-text--bold-big color-text--grey-1">(.*?)<\/strong> <p class="size-text--normal-big color-text--grey-1"><a href=".*?" >(.*?)<\/a><\/p> <\/div> <\/div> /', $response, $charge);
        unset($charge[0]);
        $renderPortlet['internet'] = implode(' ', $internet);
        $renderPortlet['voice'] = implode(' ', $voice);
        $renderPortlet['sms'] = implode(' ', $sms);
        $renderPortlet['charge'] = implode(' ', $charge);
        return $renderPortlet;
    }
}
