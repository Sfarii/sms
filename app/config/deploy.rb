set :application, "SMS"
set :domain,      "inter-hannibalalliance.org"
set :deploy_to,   "/var/www/html"
set :app_path,    "app"

set :repository,  "https://github.com/sfari/sms.git"
set :scm,         :git
set :deploy_via,  :copy
# Or: `accurev`, `bzr`, `cvs`, `darcs`, `subversion`, `mercurial`, `perforce`, or `none`
set :model_manager, "doctrine"
# Or: `propel`

role :web,        domain                         # Your HTTP server, Apache/etc
role :app,        domain, :primary => true       # This may be the same as your `Web` server
set  :keep_releases,  3
set :interactive_mode, false
# Be more verbose by uncommenting the following line
logger.level = Logger::MAX_LEVEL
set :use_composer, true
#configure the shared files
set :shared_files,      ["app/config/parameters.yml"]
set :shared_children,     [app_path + "/logs", web_path + "/images", "vendor", app_path + "/sessions"]
#Configure your server
set :use_sudo,      true
set :user, "hamza"
set :writable_dirs,       ["app/cache", "app/logs", "app/sessions"]
set :webserver_user,      "www-data"
set :permission_method,   :acl
set :use_set_permissions, true
ssh_options[:forward_agent] = true
default_run_options[:pty] = true
