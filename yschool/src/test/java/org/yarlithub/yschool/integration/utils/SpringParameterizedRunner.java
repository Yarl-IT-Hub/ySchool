//package org.yarlithub.yschool.integration.utils;
//
//import java.lang.reflect.Constructor;
//import java.lang.reflect.InvocationTargetException;
//import java.lang.reflect.Method;
//import java.lang.reflect.Modifier;
//import java.util.ArrayList;
//import java.util.Collection;
//import java.util.List;
//
//import org.junit.Assert;
//import org.junit.internal.runners.ClassRoadie;
//import org.junit.internal.runners.CompositeRunner;
//import org.junit.internal.runners.InitializationError;
//import org.junit.internal.runners.MethodValidator;
//import org.junit.internal.runners.TestClass;
//import org.junit.runner.notification.RunNotifier;
//import org.junit.runners.Parameterized;
//import org.junit.runners.Parameterized.Parameters;
//import org.springframework.test.annotation.ProfileValueUtils;
//import org.springframework.test.context.junit4.SpringJUnit4ClassRunner;
//
///**
// * This class combines Spring test support from {@link org.springframework.test.context.junit4.SpringJUnit4ClassRunner}
// * with JUnit's {@link org.junit.runners.Parameterized} runner Note that we are still using
// * JUnit's {@link org.junit.runners.Parameterized.Parameters} annotation and not our own.
// *
// * This is mostly a blending of the two classes with very little modification.
// *
// * Here is an example copied and modified from JUnit's Parameterized runner
// * class docs:
// *
// * <pre>
// * &#064;RunWith(SpringParameterizedRunner.class)
// * &#064;ContextConfiguration
// * public class FibonacciTest {
// * 	&#064;Parameters
// * 	public static Collection&lt;Object[]&gt; data() {
// * 		return Arrays.asList(new Object[][] { { 0, 0 }, { 1, 1 }, { 2, 1 },
// * 				{ 3, 2 }, { 4, 3 }, { 5, 5 }, { 6, 8 } });
// * 	}
// *
// * 	&#064;Autowired
// * 	private Foo foo;
// * 	private int fInput;
// * 	private int fExpected;
// *
// * 	public FibonacciTest(int input, int expected) {
// * 		fInput = input;
// * 		fExpected = expected;
// * 	}
// *
// * 	&#064;Test
// * 	public void test() {
// * 		assertEquals(fExpected, Fibonacci.compute(fInput));
// * 	}
// * }
// * </pre>
// */
//public class SpringParameterizedRunner  extends CompositeRunner {
//	static class SpringTestClassRunnerForParameters extends
//			SpringJUnit4ClassRunner {
//		private final Object[] fParameters;
//
//		private final int fParameterSetNumber;
//
//		private final Constructor<?> fConstructor;
//
//		SpringTestClassRunnerForParameters(TestClass testClass, Object[] parameters, int i) throws InitializationError {
//			super(testClass.getJavaClass());
//			fParameters= parameters;
//			fParameterSetNumber= i;
//			fConstructor= getOnlyConstructor();
//		}
//
//		@Override
//		protected Object createTest() throws Exception {
//
//			Object testInstance = fConstructor.newInstance(fParameters);
//			getTestContextManager().prepareTestInstance(testInstance);
//			return testInstance;
//
//		}
//
//		@Override
//		protected String getName() {
//			return String.format("[%s]", fParameterSetNumber);
//		}
//
//		@Override
//		protected String testName(final Method method) {
//			return String.format("%s[%s]", method.getName(), fParameterSetNumber);
//		}
//
//		private Constructor<?> getOnlyConstructor() {
//			Constructor<?>[] constructors= getTestClass().getJavaClass().getConstructors();
//			Assert.assertEquals(1, constructors.length);
//			return constructors[0];
//		}
//
//		@Override
//		protected void validate() throws InitializationError {
//			// do nothing: validated before.
//		}
//
//		@Override
//		public void run(RunNotifier notifier) {
//			if (!ProfileValueUtils
//					.isTestEnabledInThisEnvironment(getTestClass()
//							.getJavaClass())) {
//				notifier.fireTestIgnored(getDescription());
//				return;
//			}
//
//			runMethods(notifier);
//		}
//	}
//
//	private final TestClass fTestClass;
//
//	public SpringParameterizedRunner(Class<?> klass) throws Exception {
//		super(klass.getName());
//		fTestClass= new TestClass(klass);
//
//		MethodValidator methodValidator= new MethodValidator(fTestClass);
//		methodValidator.validateStaticMethods();
//		methodValidator.validateInstanceMethods();
//		methodValidator.assertValid();
//
//		int i= 0;
//		for (final Object each : getParametersList()) {
//			if (each instanceof Object[])
//				add(new SpringTestClassRunnerForParameters(fTestClass, (Object[])each, i++));
//			else
//				throw new Exception(String.format("%s.%s() must return a Collection of arrays.", fTestClass.getName(), getParametersMethod().getName()));
//		}
//	}
//
//	@Override
//	public void run(final RunNotifier notifier) {
//		new ClassRoadie(notifier, fTestClass, getDescription(), new Runnable() {
//			public void run() {
//				runChildren(notifier);
//			}
//		}).runProtected();
//	}
//
//	private Collection<?> getParametersList() throws IllegalAccessException, InvocationTargetException, Exception {
//		return (Collection<?>) getParametersMethod().invoke(null);
//	}
//
//	private Method getParametersMethod() throws Exception {
//		List<Method> methods= fTestClass.getAnnotatedMethods(Parameters.class);
//		for (Method each : methods) {
//			int modifiers= each.getModifiers();
//			if (Modifier.isStatic(modifiers) && Modifier.isPublic(modifiers))
//				return each;
//		}
//
//		throw new Exception("No public static parameters method on class " + getName());
//	}
//
//	public static Collection<Object[]> eachOne(Object... params) {
//		List<Object[]> results= new ArrayList<Object[]>();
//		for (Object param : params)
//			results.add(new Object[] { param });
//		return results;
//	}
//
//}
//
