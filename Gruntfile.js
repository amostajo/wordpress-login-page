/**
 * Grunt file for project.
 *
 * @author Alejandro Mostajo
 * @version 1.0
 */
module.exports = function(grunt)
{
    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        uglify: {
            dist: {
                files: {
                    'assets/dist/wp-loginpage.min.js': [
                        'bower_components/vue/dist/vue.min.js',
                        'assets/js/loginpage.js'
                    ]
                }
            }
        },
        concat: {
            options: {
                separator: ';',
            },
            dev: {
                dest: 'assets/dist/wp-loginpage.js',
                src: [
                    'bower_components/vue/dist/vue.js',
                    'assets/js/loginpage.js'
                ]
            },
        },
    });

    // Load UGLYFY
    grunt.loadNpmTasks('grunt-contrib-uglify');

    // Load CONCAT
    grunt.loadNpmTasks('grunt-contrib-concat');

    // Default task(s).
    grunt.registerTask('default', ['uglify:dist']);

    // Dev task
    grunt.registerTask('dev', ['concat:dev']);

};
