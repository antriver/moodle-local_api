Authenticating with PAM
-------------

```
apt-get install git build-essential libpam0g-dev libcurl4-openssl-dev libconfig-dev
git clone https://github.com/antriver/pam_url.git
cd pam_url
make clean all
cp pam_url.so /lib/security/pam_url.so
vi /etc/pam_url.conf
```
```
# pam_url configuration file

pam_url:
{
    settings:
    {
        url         = "https://www.mymoodle.com/local/api/auth.php"; # URI to fetch
        returncode  = "OK";                        # The remote script/cgi should return a 200 http code and this string as its only results
        userfield   = "username";                      # userfield name to send
        passwdfield = "password";                     # passwdfield name to send
        extradata   = "&mode=pam";                 # extra data to send
        prompt      = "Password: ";                   # password prompt
    };

    ssl:
    {
        use_client_cert = false;
        verify_peer = true;                               # Verify peer?
        verify_host = true;                               # Make sure peer CN matches?
    };
};

# END

```
```
vi /etc/pam.d/login
```
```
auth sufficient pam_url.so
```
