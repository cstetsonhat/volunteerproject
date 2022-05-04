<form action="index.php" method="POST" name="form_signin" class="d-flex flex-col c-m-t gap border c-padding" style="--c-m-t:3rem;--gap:1rem;--c-padding: 0.8rem">

    <div class="form_input d-flex justify-between">
        <label for="">email
        </label>
        <input type="email" required placeholder="email@domain.com" name="email" value="email@domain.com">
    </div>

    <div class="form_input d-flex justify-between">
        <label for="">password
        </label>
        <input type="password" required name="password" value="password">
    </div>


    <div class="form_input d-flex justify-between">
        <input type="submit" value="Sign in" name="submit" class="bg-primary btn"></button>

        <a href="signup.php">Signup</a>
    </div>


</form>