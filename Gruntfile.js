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
                    'assets/dist/wp-loginpage-vue.min.js': [
                        'bower_components/vue/dist/vue.min.js',
                        'assets/js/loginpage.js'
                    ],
                    'assets/dist/wp-loginpage.min.js': [
                        'assets/js/vue.component.loginpageform.js',
                    ],
                }
            }
        },
        concat: {
            options: {
                separator: ';',
            },
            dev: {
                files: {
                    'assets/dist/wp-loginpage-vue.js': [
                        'bower_components/vue/dist/vue.js',
                        'assets/js/vue.loginpage.js',
                    ],
                    'assets/dist/wp-loginpage.js': [
                        'assets/js/vue.component.loginpageform.js',
                    ],
                },
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
