<?php
namespace AppBundle\Tests\Controller\Api;

use AppBundle\Test\ApiTestCase;
use Symfony\Component\Form\FormInterface;

class ProgrammerControllerTest extends ApiTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->createUser('weaverryan');
    }


    public function testPOST()
    {
        $data = array(
            'nickname' => 'ObjectOrienter',
            'avatarNumber' => 5,
            'tagLine' => 'a test dev!'
        );

        // 1) Create a programmer resource
        $response = $this->client->post('/api/programmers', [
            'body' => json_encode($data)
        ]);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Location'));
        $this->assertStringEndsWith('/api/programmers/ObjectOrienter', $response->getHeader('Location'));
        $finishedData = json_decode($response->getBody(true), true);
        $this->assertArrayHasKey('nickname', $finishedData);
        $this->assertEquals('ObjectOrienter', $finishedData['nickname']);
    }

    public function testGETProgrammer()
    {
        $this->createProgrammer(array(
            'nickname' => 'UnitTester',
            'avatarNumber' => 3,
        ));

        $response = $this->client->get('/api/programmers/UnitTester');
        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertiesExist($response, array(
            'nickname',
            'avatarNumber',
            'powerLevel',
            'tagLine'
        ));
        $this->asserter()->assertResponsePropertyEquals($response, 'nickname', 'UnitTester');
        $this->asserter()->assertResponsePropertyEquals($response, 'uri', '/api/programmers/UnitTester');
    }

    public function testGETProgrammersCollection()
    {
        $this->createProgrammer(array(
            'nickname' => 'UnitTester',
            'avatarNumber' => 3,
        ));
        $this->createProgrammer(array(
            'nickname' => 'CowboyCoder',
            'avatarNumber' => 5,
        ));

        $response = $this->client->get('/api/programmers');
        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyIsArray($response, 'items');
        $this->asserter()->assertResponsePropertyCount($response, 'items', 2);
        $this->asserter()->assertResponsePropertyEquals($response, 'items[1].nickname', 'CowboyCoder');
    }

    public function testPUTProgrammer()
    {
        $this->createProgrammer(array(
            'nickname' => 'CowboyCoder',
            'avatarNumber' => 5,
            'tagLine' => 'foo',
        ));

        $data = array(
            'nickname' => 'CowgirlCoder',
            'avatarNumber' => 2,
            'tagLine' => 'foo',
        );
        $response = $this->client->put('/api/programmers/CowboyCoder', [
            'body' => json_encode($data)
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals($response, 'avatarNumber', 2);
        // the nickname is immutable on edit
        $this->asserter()->assertResponsePropertyEquals($response, 'nickname', 'CowboyCoder');
    }

    public function testPATCHProgrammer()
    {
        $this->createProgrammer(array(
            'nickname' => 'CowboyCoder',
            'avatarNumber' => 5,
            'tagLine' => 'foo',
        ));

        $data = array(
            'tagLine' => 'bar',
        );
        $response = $this->client->patch('/api/programmers/CowboyCoder', [
            'body' => json_encode($data)
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals($response, 'avatarNumber', 5);
        $this->asserter()->assertResponsePropertyEquals($response, 'tagLine', 'bar');
    }

    public function testDELETEProgrammer()
    {
        $this->createProgrammer(array(
            'nickname' => 'UnitTester',
            'avatarNumber' => 3,
        ));

        $response = $this->client->delete('/api/programmers/UnitTester');
        $this->assertEquals(204, $response->getStatusCode());
    }


    public function testValidationErrors()
    {
        $data = array(
            'avatarNumber' => 5,
            'tagLine' => 'a test dev!'
        );

        // 1) Create a programmer resource

        $response = $this->client->post('/api/programmers', [
            'body' => json_encode($data)
        ]);

        //var_dump($response->getBody());die;

        $this->assertEquals('application/problem+json', $response->getHeader('Content-Type'));


        $this->assertEquals(400, $response->getStatusCode());
        $this->asserter()->assertResponsePropertiesExist($response, ['type', 'title', 'errors']);

        $this->asserter()->assertResponsePropertyExists($response, 'errors.nickname');
        $this->asserter()->assertResponsePropertyExists($response, 'errors.nickname[0]', 'Please enter your clever nickname');

        $this->asserter()->assertResponsePropertyDoesNotExist($response, 'errors.avatarNumber[0]');
    }

    public function testInvalidJson()
    {

        $invalidBody= <<<EOF
        
        
         {
                 "nickname": "JohnnyRobot",
                 "avatarNumber" : "2
                "tagLine": "I'm from a test!"
         }

EOF;

        
        $response = $this->client->post('/api/programmers', [
                        'body' => $invalidBody 
                    ]);
        //var_dump($response);die;
        $this->debugResponse($response);
        $this->assertEquals(400, $response->getStatusCode());
      // $this->asserter()->assertResponsePropertyEquals($response, 'type', 'INVALID Json body !');

    }


    public function test404Exception()
    {

        $response = $this->client->get('/api/programmers/fake');
        $this->asserter()->assertEquals(404,$response->getStatusCode() );
         $this->asserter()->assertResponsePropertyEquals($response, 'detail', 'No programmer found with nickname "fake"');
        $this->asserter()->assertEquals('application/problem+json',$response->getHeader('Content-Type'));

        $this->asserter()->assertResponsePropertyEquals($response,'type', 'about:blank');
       $this->asserter()->assertResponsePropertyEquals($response, 'title', 'Not Found');
    }

    public function testGETProgrammersCollectionaPagination()
    {

        $this->createProgrammer(array(
            'nickname' => 'willnotmatch',
            'avatarNumber' => 5,
        ));
        for($i=0 ;$i<25;$i++)
        {

            $this->createProgrammer(array(
                'nickname' => 'Programmer'.$i,
                'avatarNumber' => 3,
            ));
        }


        
        $response = $this->client->get('/api/programmers?filter=Programmer');

        //var_dump($response);die;
        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals($response, 'items[5].nickname', 'Programmer5');

        $this->asserter()->assertResponsePropertyEquals($response, 'count',10 );
        $this->asserter()->assertResponsePropertyEquals($response, 'total',25 );
        $this->asserter()->assertResponsePropertyExists($response, '_links.next');

        $nextLink=$this->asserter()->readResponseProperty($response,'_links.next' );
        $response=$this->client->get($nextLink);
        $lastLink = $this->asserter()->readResponseProperty($response, '_links.last');
        $response = $this->client->get($lastLink);
        $this->asserter()->assertResponsePropertyEquals(
                   $response,
                  'items[4].nickname',
                  'Programmer24'
                      );
        $this->asserter()->assertResponsePropertyEquals($response,'items[4].nickname','Programmer24');
        $this->asserter()->assertResponsePropertyEquals($response, 'count', 5);
        $this->asserter()->assertResponsePropertyDoesNotExist($response, 'items[5].name');
        $this->asserter()->assertResponsePropertyEquals($response, 'count', 5);
    }



}
