<?php

namespace Jdm\Graffiti;

use Jdm\Graffiti\Entity\Picture;
use Jdm\Graffiti\Entity\PictureRepository;
use Jdm\Graffiti\SecurityService;
use COption;
use Imagick;
use DateTime;
use DomainException;
use RuntimeException;
use InvalidArgumentException;

class GraffityManager
{
    /**
     * @var string
     */
    private $pictureDirectoryPath;

    /**
     * @var PictureRepository
     */
    private $pictureRepository;

    /**
     * @var SecurityService
     */
    private $securityService;

    /**
     * @var int
     */
    private $graffitiWidth;

    /**
     * @var int
     */
    private $graffitiHeight;

    /**
     * @param PictureRepository $pictureRepository
     * @param SecurityService   $securityService
     * @param string            $pictureDirectoryPath
     * @param int               $graffitiWidth
     * @param int               $graffitiHeight
     */
    public function __construct(
        $pictureRepository,
        $securityService,
        $pictureDirectoryPath,
        $graffitiWidth = 600,
        $graffitiHeight = 600
    ) {
        $this->pictureDirectoryPath = trim($pictureDirectoryPath, '/');
        $this->pictureRepository = $pictureRepository;
        $this->securityService = $securityService;

        $this->graffitiWidth = $graffitiWidth;
        $this->graffitiHeight = $graffitiHeight;
    }

    /**
     * @return PictureRepository
     */
    protected function getPictureRepository()
    {
        return $this->pictureRepository;
    }

    /**
     * @return SecurityService
     */
    protected function getSecurityService()
    {
        return $this->securityService;
    }

    /**
     * @return string
     */
    public function getPictureDirectoryPath()
    {
        return $this->pictureDirectoryPath;
    }

    /**
     * @return int
     */
    public function getGraffitiWidth()
    {
        return $this->graffitiWidth;
    }

    /**
     * @return int
     */
    public function getGraffitiHeight()
    {
        return $this->graffitiHeight;
    }

    /**
     * @param  string $path
     *
     * @return string
     */
    protected function getPictureFullDirectoryPath()
    {
        $path = $this->getPictureDirectoryPath();
        $path = trim($path, '/');

        $docRoot = $GLOBALS['DOCUMENT_ROOT'];
        $uploadPath = COption::GetOptionString('main', 'upload_dir', 'upload');

        return "{$docRoot}/{$uploadPath}/{$path}";
    }

    /**
     * @param  string $pictureName
     *
     * @return string
     */
    public function getPicturePath($pictureName)
    {
        $directoryPath = $this->getPictureFullDirectoryPath();

        return $directoryPath.'/'.$pictureName;
    }

    /**
     * @param  string $pictureName
     *
     * @return string
     */
    public function getPicturePublicPath($pictureName)
    {
        $uploadPath = COption::GetOptionString('main', 'upload_dir', 'upload');
        $directoryPath = $this->getPictureDirectoryPath();

        $path = [$uploadPath, $directoryPath, $pictureName];

        return '/'.implode('/', array_filter($path));
    }

    /**
     * @param  int $id
     *
     * @return Picture|null
     */
    public function getGraffityById($id)
    {
        $pictureRepository = $this->getPictureRepository();

        return $pictureRepository->findById($id);
    }

    /**
     * @param  int $limit
     * @param  int $page
     *
     * @return Picture[]
     */
    public function getGraffityList($page = 1, $limit = null)
    {
        $pictureRepository = $this->getPictureRepository();

        return $pictureRepository->findAll($page, $limit, ['created_at' => 'DESC']);
    }

    /**
     * @return int
     */
    public function getGraffityCount()
    {
        $pictureRepository = $this->getPictureRepository();

        return (int) $pictureRepository->getCount();
    }

    /**
     * @param  Picture $picture
     *
     * @return array
     */
    public function prepareGraffityAsArray(Picture $picture)
    {
        $path = $this->getPicturePublicPath($picture->getName());

        return [
                'id' => $picture->getId(),
                'path' => $path,
                'created' => $picture->getCreatedAt(),
                'updated' => $picture->getUpdatedAt(),
            ];
    }

    /**
     * @param  string $data
     * @param  string $filename
     *
     * @return bool
     */
    protected function saveImage($data, $filename)
    {
        $data = str_replace('data:image/png;base64,', '', $data);
        $data = str_replace(' ', '+', $data);

        $data = base64_decode($data);

        $imagick = new Imagick();
        $imagick->readImageBlob($data);
        $imagick->scaleImage($this->getGraffitiWidth(), $this->getGraffitiHeight(), true);

        $path = $this->getPictureFullDirectoryPath();

        return file_put_contents($path.'/'.$filename, $imagick->getImageBlob());
    }

    /**
     * @param  string $image
     * @param  string $password
     *
     * @return int
     */
    public function createNewGraffity($image, $password)
    {
        $securityService = $this->getSecurityService();
        $pictureRepository = $this->getPictureRepository();

        if (trim(strlen($password)) == 0) {
            throw new DomainException('Пароль не задан');
        }

        $salt = $securityService->generateSalt();
        $passwordHash = $securityService->passwordHash($password, $salt);

        $filename = md5($image).time().'.png';

        $result = $this->saveImage($image, $filename);

        if (!$result) {
            throw new RuntimeException('Ошибка сохранения граффити');
        }

        $picture = new Picture($filename, $passwordHash, $salt);

        $pictureRepository->add($picture);
        $pictureRepository->commit();

        return $pictureRepository->getLastInsertId();
    }

    /**
     * @param  int    $pictureId
     * @param  string $image
     * @param  string $token
     *
     * @return int
     *
     * @throws InvalidArgumentException If graffity not found
     * @throws RuntimeException If password incorrect
     * @throws RuntimeException If save image error
     */
    public function updateGraffity($pictureId, $image, $token)
    {
        $pictureRepository = $this->getPictureRepository();
        $securityService = $this->getSecurityService();

        $picture = $pictureRepository->findById($pictureId);

        if (!$picture) {
            throw new InvalidArgumentException('Графити не найдено');
        }

        $verify = $securityService->accessTokenVerify($picture->getId(), $picture->getSalt(), $token);

        if (!$verify) {
            throw new RuntimeException('Неверный токен');
        }

        $result = $this->saveImage($image, $picture->getName());

        if (!$result) {
            throw new RuntimeException('Ошибка сохранения граффити');
        }

        $picture->setUpdatedAt(new DateTime());

        $pictureRepository->add($picture);
        $pictureRepository->commit();

        return $pictureRepository->getLastInsertId();
    }

    /**
     * @param  int $pictureId
     * @param  string $password
     *
     * @return string
     *
     * @throws InvalidArgumentException If graffity not found
     * @throws RuntimeException If password incorrect
     */
    public function getAccessToken($pictureId, $password)
    {
        $pictureRepository = $this->getPictureRepository();
        $securityService = $this->getSecurityService();

        $picture = $pictureRepository->findById($pictureId);

        if (!$picture) {
            throw new InvalidArgumentException('Графити не найдено');
        }

        $salt = $picture->getSalt();

        $verify = $securityService->passwordVerify($password, $salt, $picture->getPassword());

        if (!$verify) {
            throw new RuntimeException('Неверный пароль');
        }

        return $securityService->generateAccessToken($picture->getId(), $salt);
    }
}
