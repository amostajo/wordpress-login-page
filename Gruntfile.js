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
                    'assets/js/vendor.min.js': [
                        'node_modules/vue/dist/vue.min.js'
                    ],
                    'assets/js/app.min.js': [
                        'assets/raw/js/vue.component.loginpageform.js',
                        'assets/raw/js/vue.loginpage.js'
                    ],
                    'assets/js/component.min.js': [
                        'assets/raw/js/vue.component.loginpageform.js',
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
                    'assets/js/vendor.js': [
                        'node_modules/vue/dist/vue.min.js',
                    ],
                    'assets/js/app.js': [
                        'assets/raw/js/vue.component.loginpageform.js',
                        'assets/raw/js/vue.loginpage.js'
                    ],
                    'assets/js/component.js': [
                        'assets/raw/js/vue.component.loginpageform.js',
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
