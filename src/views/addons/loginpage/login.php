<?php get_header() ?>

    <section id="loginpage" class="login">

        <h1><?php _e( 'Login' ) ?></h1>

        <loginpage-form inline-template
            action="<?php echo $action ?>"
            token="<?php echo $token ?>"
            :defaults="{redirect_to:'<?php echo $redirect_to ?>'}"
        />
            <form @submit.prevent="submit">

                <div class="form-group">
                    <label for="user_login"><?php _e( 'Username' ) ?></label>
                    <input type="text"
                        id="user_login"
                        class="form-control"
                        v-model="formData.user_login"
                    />
                </div>

                <div class="form-group">
                    <label for="user_password"><?php _e( 'Password' ) ?></label>
                    <input type="password"
                        id="user_password"
                        class="form-control"
                        v-model="formData.user_password"
                    />
                </div>

                <div class="checkbox">
                    <label for="remember">
                        <input type="checkbox"
                            id="remember"
                            value="1"
                            v-model="formData.remember"
                        /> <?php _e( 'Remember me' ) ?>
                    </label>
                </div>

                <?php do_action( 'addon_loginpage_inside_login_form' ) ?>

                <!-- IMPORTANT: Notifications must be placed anywhere inside id="login" -->
                <section class="errors"
                    style="display: none;"
                    v-show="hasErrors"
                >
                    <div v-for="error in errors">
                        {{ error }}
                    </div>
                </section>
                <!-- IMPORTANT: Notifications must be placed anywhere inside id="login" -->

                <button type="submit"
                    class="btn btn-default"
                    v-show="!isLoading"
                >
                    <?php _e( 'Login' ) ?>
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