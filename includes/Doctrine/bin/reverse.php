<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.doctrine-project.org>.
 */
define("REVERSE_FOLDER", dirname ( __FILE__ ));

require_once '../Doctrine/Common/ClassLoader.php';

$classLoader = new \Doctrine\Common\ClassLoader('Doctrine', dirname(__FILE__) . '/../');
$classLoader->register();

$classLoader = new \Doctrine\Common\ClassLoader('Symfony', dirname(__FILE__) . '/../Doctrine/');
$classLoader->register();

$configFile = getcwd() . DIRECTORY_SEPARATOR . 'cli-config.php';


$helperSet = null;
if (file_exists($configFile)) {
    if ( ! is_readable($configFile)) {
        trigger_error(
            'Configuration file [' . $configFile . '] does not have read permission.', E_ERROR
        );
    }

    require $configFile;

    foreach ($GLOBALS as $helperSetCandidate) {
        if ($helperSetCandidate instanceof \Symfony\Component\Console\Helper\HelperSet) {
            $helperSet = $helperSetCandidate;
            break;
        }
    }
}

$helperSet = ($helperSet) ?: new \Symfony\Component\Console\Helper\HelperSet();

$em->getConfiguration()->setMetadataDriverImpl(
    new \Doctrine\ORM\Mapping\Driver\DatabaseDriver(
        $em->getConnection()->getSchemaManager()
    )
);

$cmf = new \Doctrine\ORM\Tools\DisconnectedClassMetadataFactory();
$cmf->setEntityManager($em);
$metadata = $cmf->getAllMetadata();

exec("rm -f " . REVERSE_FOLDER . "/tmp/*");

$cme = new \Doctrine\ORM\Tools\Export\ClassMetadataExporter();
$exporter = $cme->getExporter('xml', __DIR__ . "/tmp/");
$exporter->setMetadata($metadata);
$exporter->export();

exec("rm -f " . REVERSE_FOLDER . "/entities/*");
exec("php doctrine.php orm:generate-entities --generate-annotations=true " . REVERSE_FOLDER . "/entities/");
