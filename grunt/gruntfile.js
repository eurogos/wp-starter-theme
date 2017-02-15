module.exports = function(grunt) {

  // display the elapsed time after the tasks run
  require('time-grunt')(grunt);

  // Load all Grunt tasks that are listed in package.json
  require('load-grunt-tasks')(grunt);

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    jshint: {
      all: {
        src: '../assets/js/*.js',
        options: {
          curly: true,
          eqeqeq: true,
          eqnull: true,
          browser: true,
          globals: {
            jQuery: true
          }
        }
      }
    },

    postcss: {
      options: {
        processors: [
          require('autoprefixer')({browsers: 'last 2 versions'}), // add vendor prefixes
        ]
      },
      prod: {
        options: {
          processors: [
            require('autoprefixer')({browsers: 'last 2 versions'}), // add vendor prefixes
            require('cssnano')() // minify the result
          ]
        },
        files: [
          {
            expand: true,                 // Enable dynamic expansion.
            cwd: '../assets/css/',        // Src matches are relative to this path.
            src: ['*.css'],               // Actual pattern(s) to match.
            dest: '../assets/css/min/',   // Destination path prefix.
            ext: '.min.css',              // Dest filepaths will have this extension.
            extDot: 'first'               // Extensions in filenames begin after the first dot
          },
        ],
      },
      dev: {
        options: {
          processors: [
            require('autoprefixer')({browsers: 'last 2 versions'}), // add vendor prefixes
          ]
        },
        files: [
          {
            expand: true,                 // Enable dynamic expansion.
            cwd: '../assets/css/',        // Src matches are relative to this path.
            src: ['*.css'],               // Actual pattern(s) to match.
            dest: '../assets/css/min/',   // Destination path prefix.
            ext: '.min.css',              // Dest filepaths will have this extension.
            extDot: 'first'               // Extensions in filenames begin after the first dot
          },
        ],
      }
    },


    // csslint: {
    //   dev: {
    //     src: ['../assets/css/min/style.min.css']
    //   }
    // },

    imagemin: {
      prod: {
        options: {
          optimizationLevel: 3, // 3 is the default
        },
        files: [{
          expand: true,
          src: ['../dist/assets/img/**/*.{png,jpg,jpeg,gif}'],
          dest: './'
        }]
      }
    },


    uglify: {
      prod: {
        files: {
          '../assets/js/min/main.min.js': ['../assets/js/*.js']
        }
      }
    },

    sass: {
      prod: {
        // files: {
        //   '../assets/css/min/style.min.css' : '../assets/sass/style.scss'
        // },
        files: [{
          expand: true,
          cwd: '../assets/sass',
          src: ['*.scss'],
          dest: '../assets/css',
          ext: '.css'
        }],
        options: {
          sourcemap: 'none',
          style: 'expanded',
          loadPath: ['./bower_components/foundation-sites/scss']
        }
      }
    },

    clean: {
      prod: {
        src: ['../dist/'],
        options: {
          force: true
        }
      }
    },

    copy: {
      prod: {
        src: [
        '../**',
        '!../assets/sass/**',
        '!../assets/css/*',
        '!../assets/js/*',
        '!../grunt/**',
        '!../dist/**'
        ],
        expand: true,
        dest: '../dist/**',
      }
    },

    // Watch
    watch: {
      css: {
        files: '../assets/sass/*.scss',
        tasks: ['sass:prod', 'postcss:dev'],
        options: {
          livereload: true,
        },
      },

      jshint: {
        files: ['../assets/js/*.js'],
        tasks: ['jshint'],
        options: {
          livereload: true,
        },
      },

      uglify: {
        files: ['../assets/js/*.js'],
        tasks: ['uglify:prod'],
        options: {
          livereload: true,
        },
      },

      all: {
        files: ['../assets/css/min/*.css', '../assets/js/min/*.js', '../*.html', '../assets/img/*', '../*.php'],
        options: {
          livereload: true,
        },
      }
    },

    // notify cross-OS
    notify: {
      prod: {
        options: {
          title: 'Grunt, grunt!',
          message: 'Theme ready for production in /dist'
        }
      }
    }

  });

  // Grunt Tasks...

  //Remove default - I prefer to be specific!
  grunt.registerTask('default', function(){
    grunt.log.write('There is no default task - specify one please... (Watch or Build)').ok();
  });

  //Build task for shipping
  grunt.registerTask('build', 'Build the Theme for distribution', function(){
    grunt.task.run([
      // Sass
      'sass:prod',
      // Css PostProcessors
      'postcss:prod',
      // Uglify JS
      'uglify:prod',
      //Empty the distribution folder
      'clean:prod',
      //Copy the Theme contents: ignore the /grunt dir
      'copy:prod',
      //Compress the production images
      'imagemin:prod',
      //Notify Complete
      'notify:prod',
      ]);
  });

};