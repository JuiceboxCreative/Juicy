module.exports = function (grunt) {
    // Configuration goes here
    grunt.initConfig({

        pkg: grunt.file.readJSON('package.json'),

        sass: {
            development: {
                options: {
                    style: 'expanded'
                },
                files: '<%= pkg.grunt_config.scss.files %>'
            },
            production: {
                options: {
                    style: 'compressed'
                },
                files: '<%= pkg.grunt_config.scss.files %>'
            },
            options: {
                bundleExec: true
            }
        },

        autoprefixer: {
            prefix: {
                options: {
                    browsers: ['last 2 version']
                },
                src: '<%= pkg.grunt_config.css.files %>'
            }
        },

        uglify: {
            development: {
                options: {
                    beautify: true,
                    mangled: false
                },
                files: '<%= pkg.grunt_config.js.files %>'
            },
            production: {
                options: {
                    beautify: false
                },
                files: '<%= pkg.grunt_config.js.files %>'
            }
        },

        scsslint: {
            allFiles: [
                'scss/**/*.scss'
            ],
            options: {
                bundleExec: true,
                config: '.scss-lint.yml',
                colorizeOutput: true,
                maxBuffer: 30000 * 1024,
                compact: true,
                force: true,
                exclude: [
                    'scss/vendor-overrides/**/*.scss'
                ]
            }
        },

        cssmin: {
            options: {
                sourceMap: true
            },
            target: {
                files: [{
                    expand: true,
                    cwd: 'css',
                    src: ['*.css'],
                    dest: 'css'
                }]
            }
        },

        watch: {
            options: {
                debounceDelay: 250,
                livereload: true
            },

            sass: {
                files: ['<%= pkg.grunt_config.scss.folder %>' + '/**/*' + '<%= pkg.grunt_config.scss.ext %>'],
                tasks: ['sass:development', 'autoprefixer:prefix', 'cssmin', 'scsslint'],
                options: {
                    livereload: false
                }
            },

            scripts: {
                files: ['js/plugins/*.js', 'js/src/*.js'],
                tasks: ['uglify:development'],
                options: {
                    livereload: false
                }
            },

            css: {
                files: ['css/*.css']
            },

            js: {
                files: ['js/*.js']
            },

            php: {
                files: ['**/*.php']
            },

            twig: {
                files: ['views/**/*.twig']
            }
        }
    });

    // load all grunt tasks
    require('matchdep').filterDev('grunt-*').forEach(grunt.loadNpmTasks);

    // Define your tasks here
    grunt.registerTask('default', ['sass:development', 'autoprefixer:prefix', 'cssmin', 'scsslint', 'uglify:development', 'watch']);

    // Define your tasks here
    grunt.registerTask('deploy', ['scsslint', 'sass:production', 'autoprefixer:prefix', 'cssmin', 'uglify:production']);

};
