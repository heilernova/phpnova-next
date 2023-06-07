<?php
namespace Phpnova\Next\Panel\Dto;

use Phpnova\Next\Http\Validators\IsEmail;
use Phpnova\Next\Http\Validators\IsString;

class CredentialsDto
{
    #[IsEmail()]
    public readonly string $username;

    #[IsString()]
    public readonly string $passsword;
}