subinclude('//:codeneric_defs')

plainPHPFiles = glob(["**/*.plain.php"])
hackfiles = glob(["code/**/*.php"], exclude=plainPHPFiles)


copy_to_build(
    name="json",
    srcs=glob(["code/**/*.json"], exclude=["code/assets/js/manifest.json"]),
    labels = ["watchable"]  
) 
copy_to_build(
    name="assets",    
    srcs= glob(["code/assets/**"], exclude = ["code/assets/js/**"]),
    labels = ["watchable"]  
)
copy_to_build(
    name="protect_images",    
    srcs= glob(["code/protect_images/**"], exclude = ["**.php"]),
    labels = ["watchable"]  
) 

copy_to_build(
    name="root_files",    
    srcs= ["config.php","index.php","LICENSE.txt" ],
    labels = ["watchable"]  
)
 





copy_to_build(
    name="plainPHP",
    srcs=plainPHPFiles,
    labels = ["watchable"]  
)

genrule(
    name="lib",
    srcs=["//:lib"],
    outs=[build_dir("/lib")],
    cmd="cp -r $SRC $OUT",
    labels = ["watchable"]  
)

genrule(
    name="plugin_entry_php",
    srcs=["photography_management.php"],
    outs=[build_dir("/photography_management.php")],
    pass_env=["PLUGIN_ENV"],
    cmd=[
        "cp -r $SRC $OUT",    
        f'sed -i -e "s/%%PLUGIN_VERSION%%/{CONFIG.PLUGIN_VERSION}/g" $OUT',      
        f'sed -i -e "s/%%PLUGIN_ENV%%/$PLUGIN_ENV/g" $OUT'],
    labels = ["watchable"]  
)

# todo: copying the local vendor folder to build/vendor will trigger composer's cache mechanism, so we dont redownload on every build
# I did this in a train without internet connection so this might be unneccessary
genrule(
    name="composer",    
    outs=[build_dir("/vendor")],
    srcs=["//:composer_files", "//:local_vendor"],
    cmd="mv src/vendor $OUTS && COMPOSER_VENDOR_DIR=$OUTS composer install --no-dev --optimize-autoloader",
    labels = ["watchable"]  
)  

genrule(
    name="check_env",
    outs=[".check-env"],
    pass_env=["PLUGIN_ENV"],
    cmd=['[ -z "$PLUGIN_ENV" ] && echo "Need to set PLUGIN_ENV" && exit 1 || touch $OUT' ]     
)


h2tp_common_cmds = [  f'rm -rf {CONFIG.BUILD_DIR}',
            f"docker run --rm -v $(pwd):$(pwd) -u $UID:$(id -g)  codeneric/hack-transpiler h2tp $HOME/src/base/code $HOME/" + build_dir('/code'),
            
            f'sed -i -e "s/%%PLUGIN_VERSION%%/{CONFIG.PLUGIN_VERSION}/g" $OUTS',

            # fix for the questionmark issue
            # I do not now why that many backslashes, but otherwise wont work
            f'sed -i -e "s#\\\\\?#?#g" $OUTS',          
            f'sed -i -e "s/%%PLUGIN_ENV%%/$PLUGIN_ENV/g" $OUTS', 
            "exit 1"
            ]
genrule(
    name="h2tp",
    deps=["//:config_files", ":check_env"],
    srcs=hackfiles,
    outs=[build_dir("/" + w) for w in hackfiles],
    tools=["//build-tools:demo_regex"],
    pass_env=["PLUGIN_ENV"],
    
    cmd= select({ 
        '//:demo_build' : ' && '.join([           
            "echo 'Demo not fixed yet'", "exit 1",   
            "$(exe //build-tools:demo_regex) --files",   
            f'rm -rf {CONFIG.BUILD_DIR}',
            f"docker run --rm -v $(pwd):$(pwd) -u $UID:$(id -g)  codeneric/hack-transpiler h2tp $HOME/src/base/code $HOME/" + build_dir('/code'),
            f'sed -i -e "s/%%PLUGIN_VERSION%%/{CONFIG.PLUGIN_VERSION}/g" $OUTS',
            # fix for the questionmark issue
            # I do not now why that many backslashes, but otherwise wont work
            f'sed -i -e "s#\\\\\?#?#g" $OUTS',          
            f'sed -i -e "s/%%PLUGIN_ENV%%/$PLUGIN_ENV/g" $OUTS',] ),
        'default' : ' && '.join([
            f'rm -rf {CONFIG.BUILD_DIR}',
            f"docker run --rm -v $(pwd):$(pwd) -u $UID:$(id -g)  codeneric/hack-transpiler h2tp $HOME/src/base/code $HOME/" + build_dir('/code'),
            f'sed -i -e "s/%%PLUGIN_VERSION%%/{CONFIG.PLUGIN_VERSION}/g" $OUTS',
            # fix for the questionmark issue
            # I do not now why that many backslashes, but otherwise wont work
            f'sed -i -e "s#\\\\\?#?#g" $OUTS',          
            f'sed -i -e "s/%%PLUGIN_ENV%%/$PLUGIN_ENV/g" $OUTS', ]) 
    }),
    labels = ["watchable"]      
)

genrule(
    name="readme",
    srcs=["README.txt"],
    outs=[build_dir("/README.txt")],  
    deps=["//:config_files"],
    tools=["//build-tools:mutate-readme"],
    cmd=f"$(exe //build-tools:mutate-readme) --file=$SRCS --pluginFile=$HOME/plugin.json --version {CONFIG.BUILD_DIR} --target base > $OUT",
  
    labels = ["watchable"]  
    #post_build=lambda name, output: add_out("readme", "thefile.lig"),
)

  
genrule( 
    name = "lint_hack",
    tools=["//build-tools:hh_client_log_parse"],
    srcs = hackfiles,
    deps = ["//:config_files", "//:hack_deps"],
    outs = ["hh_client.log"],
    cmd = "(docker run --rm -v $(pwd):$(pwd) -u $UID  codeneric/hack-transpiler hh_client check $HOME > $OUT || exit 0) &&  cat $OUT | $(exe //build-tools:hh_client_log_parse) --colors",
    labels = ["lint"]  
)   


# todo: use post_build to add_output to this rule by reading webpacks --json output so Please knows all generated files
make_webpack_rule(target = "base")






filegroup(
    name = "build_without_webpack",
    srcs = [
        ":assets", 
        ":protect_images",
        ":root_files",
        ":plugin_entry_php", 
        ":plainPHP",":h2tp", ":lib", ":composer", ":json", ":readme"],
    labels = ["watchable"]  
)
filegroup(
    name = "build",
    srcs = [
        ":build_without_webpack", ":webpack"],
   
)

genrule(     
    name="zip",
    deps=[":build"],
    outs= select({
        '//:demo_build': ["demo-photography-management.zip"],
        'default': ["photography-management.zip"]
    }),
    cmd= f"mv src/base/{CONFIG.BUILD_DIR}/ $TMPDIR/photography-management/ && cd $TMPDIR &&  zip -r $OUTS photography-management/"
)  
# this is just an alias for shorter command
filegroup(
    name ="base",
    srcs=[":zip"]

)

