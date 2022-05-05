<form action="signup.php?action=sign_up" method="POST" name="form_signin" class="d-flex flex-col c-m-t gap border c-padding" style="--c-m-t:3rem;--gap:1rem;--c-padding: 0.8rem">

    <div class="form_input d-flex justify-between">
        <label for="">email
        </label>
        <input type="email" required placeholder="email@domain.com" name="email" value="email@domain.com">
    </div>

    <div class="form_input d-flex justify-between">
        <label for="">firstname
        </label>
        <input type="text" required  name="firstname" value="grace">
    </div>

    <div class="form_input d-flex justify-between">
        <label for="">lastname
        </label>
        <input type="text" required  name="lastname" value="hopper">
    </div>

    <div class="form_input d-flex justify-between">
        <label for="">occupation
        </label>
        <input type="occupation" required placeholder="email@domain.com" name="occupation" value="doctor">
    </div>

    <div class="form_input d-flex justify-between">
        <label for="">password
        </label>
        <input type="password" required name="password" value="password">
    </div>
    <div class="form_input d-flex justify-between">
        <label for="">password_confirm
        </label>
        <input type="password" required name="password_confirm" value="password">
    </div>


    <div class="form_input d-flex justify-between">
        <input type="submit" name="submit" value="Sign up"  class="bg-primary btn"></button>

        <a href="index.php">Sign in</a>
    </div>


</form>