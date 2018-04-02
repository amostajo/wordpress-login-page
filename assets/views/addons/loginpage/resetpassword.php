<?php get_header() ?>

    <section id="loginpage" class="resetpassword">

        <h1><?php _e( 'Reset your password' ) ?></h1>

        <p><?php _e( 'This link will be invalid once finished.' ) ?></p>

        <loginpage-form inline-template
            action="<?php echo $action ?>"
            token="<?php echo $token ?>"
            :defaults="{ID:'<?php echo $user->ID ?>'}"
        />
            <form @submit.prevent="submit">

                <div class="form-group">
                    <label for="user_pass"><?php _e( 'New password' ) ?></label>
                    <input type="password"
                        id="user_pass"
                        class="form-control"
                        v-model="formData.user_pass"
                    />
                </div>

                <div class="form-group">
                    <label for="user_login"><?php _e( 'Repeat password' ) ?></label>
                    <input type="password"
                        id="repeat_pass"
                        class="form-control"
                        v-model="formData.repeat_pass"
                    />
                </div>

                <?php do_action( 'addon_loginpage_inside_reset_password_form' ) ?>

                <!-- IMPORTANT: Error notifications must be placed anywhere inside id="signup" -->
                <section class="errors"
                    style="display: none;"
                    v-show="hasErrors"
                >
                    <div v-for="error in errors" v-html="error"></div>
                </section>
                <!-- IMPORTANT: Notifications must be placed anywhere inside id="signup" -->

                <!-- IMPORTANT: Notifications must be placed anywhere inside id="signup" -->
                <section class="success"
                    style="display: none;"
                    v-show="message"
                    v-html="message"
                ></section>
                <!-- IMPORTANT: Notifications must be placed anywhere inside id="signup" -->

                <button type="submit"
                    class="btn btn-default"
                    v-show="!isLoading"
                >
                    <?php _e( 'Reset' ) ?>
                </button>

                <span span="loading"
                    style="display: none;"
                    v-show="isLoading"
                >
                    <?php _e( 'Loading...' ) ?>
                </span>

            </form>
        </loginpage-form>

    </section>

<?php get_footer() ?>