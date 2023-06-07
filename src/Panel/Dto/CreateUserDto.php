<?php
namespace Phpnova\Next\Panel\Dto;

use Phpnova\Next\Http\Attributes\Body;
use Phpnova\Next\Http\Validators\IsEmail;

#[Body]
class CreateUserDto
{
    #[IsEmail]
    public string $email;
}